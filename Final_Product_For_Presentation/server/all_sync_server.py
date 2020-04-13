# !/usr/bin/env python

# WS server example that synchronizes state across clients

import asyncio
import json
import logging
import websockets

logging.basicConfig()

STATE = {"value": 0}

USERS = set()
CONTROLLER = set()


def state_event():
    return json.dumps({"type": "state", **STATE})


def users_event():
    return json.dumps({"type": "users", "count": len(USERS)+len(CONTROLLER)})


def playing_event():
    return json.dumps({"type": "masterPLAYING"})


def paused_event():
    return json.dumps({"type": "masterPAUSED"})


def url_event(vid):
    return json.dumps({"type": "url", "videoid": vid})

def seektomillis_event(milli):
    return json.dumps({"type": "seektomillis", "millis": milli})

async def notify_state():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = state_event()
        await asyncio.wait([user.send(message) for user in USERS])


async def notify_users():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = users_event()
        await asyncio.wait([user.send(message) for user in USERS])


async def notify_playing():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = playing_event()
        await asyncio.wait([user.send(message) for user in USERS])


async def notify_paused():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = paused_event()
        await asyncio.wait([user.send(message) for user in USERS])


async def notify_url(vid):
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = url_event(vid)
        await asyncio.wait([user.send(message) for user in USERS])

async def notify_controlling(websocket):
    if USERS:  # asyncio.wait doesn't accept an empty list
        if len(CONTROLLER) !=0:
            USERS.add(CONTROLLER.pop())
        CONTROLLER.clear()
        CONTROLLER.add(websocket)
        USERS.remove(websocket)
        message1 = json.dumps({"type": "controller"})
        await asyncio.wait([coner.send(message1) for coner in CONTROLLER])
        message2 = json.dumps({"type": "viewer"})
        await asyncio.wait([user.send(message2) for user in USERS])
        print(str(websocket)+" is taking controlling...")

async def notify_release_controlling(websocket):
    if USERS:  # asyncio.wait doesn't accept an empty list
        CONTROLLER.remove(websocket)
        USERS.remove(websocket)

async def notify_seektomillis(milli):
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = seektomillis_event(milli)
        for user in USERS:
            print("sending to ", user, ": ", message)
        await asyncio.wait([user.send(message) for user in USERS])

async def register(websocket):
    USERS.add(websocket)
    await notify_users()


async def unregister(websocket):
    USERS.remove(websocket)
    await notify_users()


async def counter(websocket, path):
    # register(websocket) sends user_event() to websocket
    await register(websocket)
    try:
        await websocket.send(state_event())
        async for message in websocket:
            data = json.loads(message)

            if len(CONTROLLER) != 0:
                ws = CONTROLLER.pop()
                CONTROLLER.add(ws)
                print(ws)
                if websocket == ws:
                    if data["action"] == "masterPLAYING":
                        await notify_playing()
                        print("masterPLAYING")

                    elif data["action"] == "masterPAUSED":
                        await notify_paused()
                        print("masterPAUSED")

                    elif (data["action"])[0:3] == "url":
                        await notify_url((data["action"][3:]))
                        print("recieved URL: " + (data["action"])[3:])

                    elif (data["action"])[0:8] == "mcurtime":
                        print("Received master's curtime: " + (data["action"])[8:])
                        if len(USERS) is not 0:
                            await notify_seektomillis(data["action"][8:])
                            print("VIEWERS not empty, sending to VIEWERS: " + (data["action"])[8:])
                    else:
                        logging.error("unsupported event: {}", data)

            if (data["action"]) == "controlling":
                await notify_controlling(websocket)

    finally:
        await unregister(websocket)

address = '192.168.2.88'
port = 6688

start_server = websockets.serve(counter, address, port)
print("ALL-SYNC-SERVER is running on port:", port)
asyncio.get_event_loop().run_until_complete(start_server)
asyncio.get_event_loop().run_forever()
