#!/usr/bin/env bash

docker build -t ecotoneframework/ecotone-pulse:$1 .
docker push ecotoneframework/ecotone-pulse:$1