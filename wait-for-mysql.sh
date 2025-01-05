#!/bin/sh

set -e

host="$1"
shift
cmd="$@"

# Ожидание запуска MySQL
until mysql -h "$host" -u root -proot -e "SELECT 1"; do
  >&2 echo "MySQL is unavailable - sleeping"
  sleep 10
done

>&2 echo "MySQL is up - executing command"

# Выполнение команды с обработкой ошибок
if ! eval "$cmd"; then
  >&2 echo "Command failed: $cmd"
  exit 1
fi

echo "Command executed successfully."