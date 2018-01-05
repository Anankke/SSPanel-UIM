all: clean test

test:
	vendor/bin/phpunit $(TEST)

coverage:
	vendor/bin/phpunit --coverage-html=artifacts/coverage $(TEST)

view-coverage:
	open artifacts/coverage/index.html

check-tag:
	$(if $(TAG),,$(error TAG is not defined. Pass via "make tag TAG=4.2.1"))

tag: check-tag
	@echo Tagging $(TAG)
	chag update $(TAG)
	git commit -a -m '$(TAG) release'
	chag tag
	@echo "Release has been created. Push using 'make release'"
	@echo "Changes made in the release commit"
	git diff HEAD~1 HEAD

release: check-tag
	git push origin master
	git push origin $(TAG)

clean:
	rm -rf artifacts/*
