ARCH = $(shell dpkg --print-architecture)
OS_CODENAME = $(shell . /etc/os-release && echo "$$VERSION_CODENAME")
DOCKER_VERSION=5:24.0.7-1~ubuntu.22.04~$(OS_CODENAME)
TZ=Europe/Warsaw

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

init:
	ln -snf /usr/share/zoneinfo/$(TZ) /etc/localtime && echo $(TZ) > /etc/timezone
	@make install-deps

start:
	systemctl start docker
	docker compose up -d

