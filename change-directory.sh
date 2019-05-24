if [ -d "$1" ]
then
    cd "$1"
    exec "$SHELL"
fi

