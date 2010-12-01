#!/bin/bash

. activate.sh "${1}"

echo

if [ ! -n "${SMAPENV}" ]; then
    echo -e "\t- ** FATAL: You must provide a valid env name ('dev', 'stage', etc.).\n"
    exit 1
fi

echo ""
echo -e "\t- Set env to \"${SMAPENV}\"."

if [ ! -n "${2}" ]; then
    echo -e "\t- ** FATAL: No up/dn parameter given.\n"
    exit 1
fi

case "${2}" in

"up"|"dn")
    echo -e "\t- Setting migration direction to \"${2}\"."
    mdir="${2}"
    ;;
*)
    echo -e "\t** FATAL: Up/dn parameter \"${2}\" invalid.\n"
    exit 1
    ;;
esac

SMAP_ROOTDIR=$(dirname $(dirname $(readlink -f "${0}")))
SMAP_SCHEMADIR="${SMAP_ROOTDIR}/db/schema/"

if [ "${mdir}" = "dn" ]; then
    sortcmd="sort -r"
else
    sortcmd="sort"
fi

SCHEMAFILES=$(find "${SMAP_SCHEMADIR}" -name "*.${mdir}.sql" | $sortcmd) 

for sfile in ${SCHEMAFILES[@]}
do
    echo "${sfile}"
done

echo
