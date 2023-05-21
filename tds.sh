#!/bin/sh
while read name val; do
    if [ "$name" = Threads: ]; then
        printf %s\\n "$val"
        break
    fi
done < /proc/"$1"/status
