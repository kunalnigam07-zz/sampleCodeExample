#!/bin/sh

ssh \
    -L 20001:localhost:20001 \
    -o StrictHostKeyChecking=no -o Port=22 \
    -o UserKnownHostsFile=/dev/null \
muly@dev
