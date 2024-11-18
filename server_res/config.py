"""
This file is used to load the environment variables from the .env file.
"""

import os
from dotenv import load_dotenv

# loading the .env file
load_dotenv()

# setting the environment variables
SERVER_IP = os.getenv("SERVER_IP")
SERVER_PORT =int(os.getenv("SERVER_PORT"))
LOGIN = os.getenv("LOGIN")
PW = os.getenv("PASSWORD")
DBNAME = os.getenv("DBNAME")

# checking if all the variables are defined
if not all([SERVER_IP, SERVER_PORT, LOGIN, PW, DBNAME]):
    raise EnvironmentError("one or more environment variables are not defined")