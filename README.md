### Initial Server Setup
- Install make, on ubuntu you can run
    ```
    chmod u+x bin/get-make
    ./bin/get-make
    ```
- Install dependencies and build+start docker services
    ```
    make init
    ```

### Initial Local Setup
- All secrets are stored encrypted in this repo and they will be automatically decrypted when you checkout changes and encrypted when you commit them.
    All you need is to get the encryption passphrase, you can find it in our bldr bitwarden under name `[Humanitas] Transcrypt passphrase`.
    Once you have it, just run from root of the project
    ```
	./bin/transcrypt -c aes-256-cbc
    ```
    When asked, paste passphrase you got from bitwarden. 

### Manual Server Deploy
- Manually update db with required changes
- Upload local code onto destination machine (IP can change with time) and restart containers using command
    ```
    make sync-repo
    ```
### Pull files from Server
- If for some reason changes were made in wp-content outside of the repository, you can pull all files using following command
    ```
    make pull-server-files
    ```

### You can tunnel connection to remote database over ssh using following command
    ```
    make tunnel-db
    ```

### Start wp locally with or without tunneled db (choose 1 command)
    ```
    make dev-up
    make dev-tunnel-up
    ```

