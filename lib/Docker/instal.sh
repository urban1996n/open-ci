#!/bin/bash

if [ -z "$(which docker-compose)" ]
  then echo "docker-compose package is required to use this application exitting now"
  exit 1
fi

function createEnv(){
 loop_index=0

 if [ ! -f "${local_env_file}" ]
 then
   touch "${local_env_file}"
 fi

 declare -a vars
 declare -a existing_vars

 while IFS= read -r line
 do
   var_name="$(echo "${line}" | cut -d '=' -f1)"
   var_value="$(echo "${line}" | cut -d '=' -f2)"
   if [ "$var_value" ]
   then
     existing_vars+="$var_name"
   fi
 done < "${local_env_file}"

 while IFS= read -r line
 do
     if [[ "${line}" == \!###* || -z "${line}" ]]
       then continue
     fi

     if [[ "${line}" == \#* ]]
     then
         #Strip the comment sign from the .env comment
         comment="$(echo "${line}" | sed s/#//g)"
     else
         #Make sure only variable name will get extracted
         variable="$(echo "${line}" | cut -d '=' -f1)"
         vars+=([$loop_index]="$variable":"$comment")
         ((loop_index++))
     fi
 done < "${env_file}"

 #Ask for env variable value
 for variable in "${vars[@]}"
 do
   varname="$(echo "${variable}" | cut -d ':' -f1)"
   comment="$(echo "${variable}" | cut -d ':' -f2)"
   if [[ -z "${!varname}" && ! "${existing_vars[@]}" =~ "$varname" ]]; then
     read -p "Please specify $varname, $comment:" $varname
     echo "${varname}=${!varname}" >> "${local_env_file}"
   fi
 done
}

docker compose --env-file "${local_env_file}" up -d --build

if [ $? != 0 ]
then
  exit 1;
fi
