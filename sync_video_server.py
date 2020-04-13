#!/usr/bin/env python

# WS server example that synchronizes state across clients

import asyncio
import json
import logging
import websockets

logging.basicConfig()

STATE = {"value": 0}

USERS = set()



def state_event():
    return json.dumps({"type": "state", **STATE})

def users_event():
    return json.dumps({"type": "users", "count": len(USERS)})

def playing_event():
    return json.dumps({"type": "masterPLAYING"})

def paused_event():
    return json.dumps({"type": "masterPAUSED"})

def url_event(vid):
    return json.dumps({"type": "url", "videoid": vid})

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
            print((data["action"])[0:3])

            if data["action"] == "masterPLAYING":
                await notify_playing()
                print("masterPLAYING")
            elif data["action"] == "masterPAUSED":
                await notify_paused()
                print("masterPAUSED")
            elif (data["action"])[0:3] == "url":
                await notify_url((data["action"][3:]))
                print((data["action"])[3:])
            else:
                logging.error("unsupported event: {}", data)
    finally:
        await unregister(websocket)


start_server = websockets.serve(counter, "localhost", 6789)
print("runing",6789)
asyncio.get_event_loop().run_until_complete(start_server)
asyncio.get_event_loop().run_forever()
