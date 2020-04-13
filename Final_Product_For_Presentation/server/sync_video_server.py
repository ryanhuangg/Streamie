import asyncio
import json
import logging
import websockets

logging.basicConfig()

STATE = {"value": 0}

USERS = set()
VIEWERS = set()
MASTER = set()
muid = []
vruid = []


#EVENT FUNCTIONS-----------------------------------------------------
def state_event():
    return json.dumps({"type": "state", **STATE})

def users_event():
    return json.dumps({"type": "users", "count": len(USERS)})

def playing_event():
    return json.dumps({"type": "masterPLAYING"})

def paused_event():
    return json.dumps({"type": "masterPAUSED"})

#url_event(vid) is for server to update the changed youtube video's url to all other viewers
def url_event(vid):
    return json.dumps({"type": "url", "videoid": vid})

#front end need to use seekTOMillis(int) to seek to given time
#https://developers.google.com/youtube/android/player/reference/com/google/android/youtube/player/YouTubePlayer.PlaybackEventListener#onSeekTo(int)
def seektomillis_event(milli):
    return json.dumps({"type": "seektomillis", "millis": milli})

#NOTIFY ALL------------------------------------------------------------
async def notify_state():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = state_event()
        await asyncio.wait([user.send(message) for user in USERS])

async def notify_users():
    if USERS:  # asyncio.wait doesn't accept an empty list
        for user in USERS:
            print("counting USERS: ", user)
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

async def notify_seektomillis(milli):
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = seektomillis_event(milli)
        for user in VIEWERS:
            print("sending to ", user,": ",message)
        await asyncio.wait([user.send(message) for user in VIEWERS])

#NOTIFY VIEWERS---------------------------------------------------------
async def notify_playing_to_viewers():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = playing_event()
        for user in VIEWERS:
            print("sending to ", user,": ",message)
        await asyncio.wait([user.send(message) for user in VIEWERS])

async def notify_paused_to_viewers():
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = paused_event()
        for user in VIEWERS:
            print("sending to ", user,": ",message)
        await asyncio.wait([user.send(message) for user in VIEWERS])

async def notify_url_to_viewers(vid):
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = url_event(vid)
        for user in VIEWERS:
            print("sending to ", user,": ",message)
        await asyncio.wait([user.send(message) for user in VIEWERS])

async def notify_seektomillis_to_viewers(milli):
    if USERS:  # asyncio.wait doesn't accept an empty list
        message = seektomillis_event(milli)
        for user in VIEWERS:
            print("sending to ", user,": ",message)
        await asyncio.wait([user.send(message) for user in VIEWERS])

#REGISTER FUNCTIONS----------------------------------------------------
async def register(websocket):
    USERS.add(websocket)
    print("client added: ",websocket)
    await notify_users()

async def unregister(websocket):
    USERS.remove(websocket)
    print("client removed: ", websocket)
    await notify_users()

async def register_viewer(websocket):
    VIEWERS.add(websocket)
    print("viewer added: ", websocket)
    #await notify_users()

async def unregister_viewer(websocket):
    VIEWERS.remove(websocket)
    print("viewer removed: ", websocket)
    #await notify_users()

async def register_master(websocket):
    MASTER.add(websocket)
    print("master added: ", websocket)
    #await notify_users()

async def unregister_master(websocket):
    MASTER.remove(websocket)
    print("master removed: ", websocket)
    #await notify_users()

#COUNTER FUNCTION--------------------------------------------------------
async def listening(websocket, path):
    # register(websocket) sends user_event() to websocket
    await register(websocket)
    try:
        await websocket.send(state_event())
        async for message in websocket:
            print("websocket info: ", message)
            data = json.loads(message)
            if (data["action"])[0:4] == "muid":
                print("Master: ", websocket, " - Registered for", (data["action"])[4:])
                await register_master(websocket)
                muid.append((((data["action"])[4:]),websocket))


            elif (data["action"])[0:5] == "vruid":
                print("Viewer: ", websocket, " - Registered for", (data["action"])[5:])
                await register_viewer(websocket)
                vruid.append((((data["action"])[5:]), websocket))


            elif data["action"] == "masterPLAYING":
                print("Recieved: masterPLAYING; from: ", websocket)
                if len(VIEWERS) is not 0:
                    await notify_playing()


            elif data["action"] == "masterPAUSED":
                print("Recieved: masterPAUSED; from: ", websocket)
                if len(VIEWERS) is not 0:
                    await notify_paused()


            elif (data["action"])[0:3] == "url":
                print("Received new video URL: " + (data["action"])[3:])
                if len(VIEWERS) is not 0:
                    await notify_url((data["action"][3:]))


            elif (data["action"])[0:8] == "mcurtime":
                print("Received master's curtime: " + (data["action"])[8:])
                if len(VIEWERS) is not 0:
                    await notify_seektomillis(data["action"][8:])
                    print("VIEWERS not empty, sending to VIEWERS: " + (data["action"])[8:])


            else:
                logging.error("unsupported event: {}", data)
    finally:
        await unregister(websocket)


#START SERVER----------------------------------------------------------------
# server connection
address = '192.168.2.88'
port = 6699

# # Neal connection
# address = "localhost"
# port = 60001

## ssl method for websocket secure connection
# ssl_context = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
# ssl_context.load_cert_chain(WEBSOCKET_SSL_CERT_FILE_PATH, WEBSOCKET_SSL_KEY_FILE_PATH)
# start_server = websockets.serve(listening, address, port, ssl=ssl_context)

start_server = websockets.serve(listening, address, port)
print("Server is running on address: ", address)
print("Server is running on port: ", port)
asyncio.get_event_loop().run_until_complete(start_server)
asyncio.get_event_loop().run_forever()
