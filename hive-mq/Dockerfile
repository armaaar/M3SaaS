FROM hivemq/hivemq-ce:2020.6

# Disallow all clients extension
ENV HIVEMQ_ALLOW_ALL_CLIENTS "false"

# Update configurations
RUN mv /opt/hivemq-ce-2020.6/conf/config.xml /opt/hivemq-ce-2020.6/conf/config-standard.xml
COPY ./config.xml /opt/hivemq-ce-2020.6/conf/

## update extensions
COPY ./extensions/ /opt/hivemq-ce-2020.6/extensions/

# Expose MQTT port
EXPOSE 1883
# Expose MQTT over Websockets
EXPOSE 8000
# Expose Heartbeat HTTP port
EXPOSE 9090
