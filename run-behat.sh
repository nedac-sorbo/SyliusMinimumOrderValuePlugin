#!/usr/bin/env bash
set -eux

if [[ $(cd tests/Application && git status | awk '{print $3}' | head -n1) == "master" ]]; then
	vendor/bin/behat --tags=@theme_setup --strict -vvv --no-interaction "$@"|| vendor/bin/behat --tags=@theme_setup --strict -vvv --no-interaction --rerun "$@"
	(cd tests/Application && bin/console ca:cl)
	vendor/bin/behat --tags="@theme" --strict -vvv --no-interaction "$@" || vendor/bin/behat --tags="@theme" --strict -vvv --no-interaction --rerun "$@"
else
	vendor/bin/behat --tags="~@theme&&~@theme_setup" --strict -vvv --no-interaction "$@" || vendor/bin/behat --tags="~@theme&&~@theme_setup" --strict -vvv --no-interaction --rerun "$@"
fi
