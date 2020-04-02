#!/bin/bash

# run `su ben` to tag new files to ben
addgroup --gid 1000 ben
adduser --disabled-password --gecos "" --force-badname --ingroup ben ben
