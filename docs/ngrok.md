# ngrok

Installed by docker

create https://ngrok.com account and get auth token

create file .ngrok.yml and replace <>
------------------------------------------------
authtoken: <token>

tunnels:
  app:
    addr: 20001
    proto: http
    bind-tls: true
    subdomain: fw-<unique-name>
------------------------------------------------

(cd app; npm run deploy-local)
(cd app; npm run ngrok)


Url should look like

https://app-muly.eu.ngrok.io
https://api-muly.eu.ngrok.io
