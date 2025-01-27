#!/usr/bin/make

SHELL = /bin/sh

UID := $(shell id -u)
GID := $(shell id -g)

export UID
export GID

shell:
	docker exec -it transaction-email-sys-app-container bash -c "sudo -u app-user /bin/bash"

up:
	UID=${UID} GID=${GID} docker compose -f docker-compose.yml up --build -d --remove-orphans

down:
	docker compose -f docker-compose.yml down --remove-orphans