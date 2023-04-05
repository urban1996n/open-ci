#!/bin/bash
if [ -z "$(which docker-compose)" ]
  then echo "docker-compose package is required to use this application exiting now"
  exit 1
fi

source "${BASH_SOURCE%/*}/create_env.sh"

docker_env_schema="${BASH_SOURCE%/*}/../Docker/.env"
docker_env_file="${BASH_SOURCE%/*}/../Docker/.env.local"

project_env_schema="${BASH_SOURCE%/*}/../.env"
project_env_file="${BASH_SOURCE%/*}/../.env.local"

createEnv "$docker_env_file" "$docker_env_schema"
createEnv "$project_env_file" "$project_env_schema"

if [ $? != 0 ]
then
  exit 1;
fi

docker compose --env-file "${local_env_file}" up -d --build

if [ $? != 0 ]
then
  exit 1;
fi
