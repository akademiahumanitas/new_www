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
    Once you have it, just run 
    ```
    make repo-init
    ```
    When asked, paste passphrase you got from bitwarden. 

### Manual Server Deploy
- Manually update db with required changes
- Upload local code onto destination machine (IP can change with time)
    ```
    make sync-repo
    ```
