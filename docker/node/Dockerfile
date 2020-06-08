FROM node:14.4-alpine

ARG USER_ID
ARG GROUP_ID

RUN apk update && apk add --no-cache \
    shadow

# Set IDs from our local user
RUN usermod -u ${USER_ID} node && groupmod -g ${GROUP_ID} node || true
USER "${USER_ID}:${GROUP_ID}"
