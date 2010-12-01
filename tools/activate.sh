#!/bin/bash

if [ -n "${1}" ]; then
    if [ -f "env/${1}" ]; then
        SMAPENV="${1}"
        . "env/${SMAPENV}"
    fi
fi
