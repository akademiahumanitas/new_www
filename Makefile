ARCH = $(shell dpkg --print-architecture)
OS_CODENAME = $(shell . /etc/os-release && echo "$$VERSION_CODENAME")
DOCKER_VERSION=5:24.0.7-1~ubuntu.22.04~$(OS_CODENAME)
TZ=Europe/Warsaw
IP=148.81.198.20
USER=wp-user

get-transcrypt:
	curl -s -L https://github.com/elasticdog/transcrypt/archive/refs/tags/v2.2.3.tar.gz | tar zxf - -C bin/
	mv bin/transcrypt-2.2.3/transcrypt bin/
	rm -r bin/transcrypt-2.2.3

install-deps:
	apt-get update
	apt-get -y install vim
	@make install-docker

install-docker:
	apt-get -y install ca-certificates curl gnupg
	install -m 0755 -d /etc/apt/keyrings
	test -f /etc/apt/keyrings/docker.gpg || curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg && chmod a+r /etc/apt/keyrings/docker.gpg
	printf 'deb [arch=%s signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu %s stable\n' $(ARCH) $(OS_CODENAME) | \
		tee /etc/apt/sources.list.d/docker.list > /dev/null
	apt-get update
	apt-get -y install docker-ce=$(DOCKER_VERSION) docker-ce-cli=$(DOCKER_VERSION) containerd.io docker-buildx-plugin docker-compose-plugin
	systemctl enable docker

create-user: 
	if ! id $(USER); \
		then useradd -m -g docker -s /bin/bash $(USER) && \
		chown -R $(USER): /app && \
		mkdir -p /home/$(USER)/.ssh/ && \
		cp ./authorized_keys /home/$(USER)/.ssh/ && \
		chown $(USER): -R /home/$(USER)/.ssh && \
		chmod 644 /home/$(USER)/.ssh/authorized_keys && \
		echo 'AllowUsers root $(USER)' >> /etc/ssh/sshd_config && \
		systemctl restart sshd; \
	fi

init:
	# ln -snf /usr/share/zoneinfo/$(TZ) /etc/localtime && echo $(TZ) > /etc/timezone
	# @make install-deps
	@make create-user
	systemctl start docker
	docker compose up -d


dev-up:
	docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build wp

dev-tunnel-up:
	docker compose -f docker-compose.yml -f docker-compose.dev-tunnel.yml up -d --build wp

up:
	docker compose up -d --build

down:
	docker compose down

volume-cp:
	docker compose cp wp-content/. wp:/var/www/html/wp-content
	docker compose exec wp /bin/bash -c 'chown -R www-data:www-data /var/www/html/wp-content'

docker-clean:
	docker rmi $$(docker images -aq) -f
	docker network rm $$(docker network ls -q) -f
	docker volume rm $$(docker volume ls -q) -f
	yes | docker system prune

sync-repo:
	rsync -r . $(USER)@$(IP):/app/
	ssh $(USER)@$(IP) 'cd /app && make volume-cp && make up'

sync-keys:
	rsync authorized_keys $(USER)@$(IP):~/.ssh/authorized_keys

pull-server-files:
	rsync -r --exclude 'ai1wm-backups' $(USER)@$(IP):/app/wp-content/* wp-content/
pull-server-files-progress:
	rsync -r --info=progress2 --no-i-r --exclude 'ai1wm-backups' $(USER)@$(IP):/app/wp-content/* wp-content/

ssh:
	ssh $(USER)@$(IP)

exec-wp:
	docker compose exec -it wp /bin/bash

exec-db:
	docker compose exec -it db /bin/bash

exec-nginx:
	docker compose exec -it nginx /bin/sh

tunnel-db:
	ssh -NL 0.0.0.0:3306:localhost:3306 $(USER)@$(IP)

backup-wp:
