# Birthday Greetings Kata (PHP Version)

This is a simple refactoring exercise focused on teaching the dependency inversion and dependency injection principles.
The original Java version and kata explanations can be found in the [original post](http://matteo.vaccari.name/blog/archives/154).


## How to get started

Make sure you have PHP 8.1 or higher. Clone the repository in a separate folder. You should have `docker` and `docker compose`
installed, otherwise acceptance tests won't work. On the other hand, you may solve the kata without them (not recommended).

Clone the repository. Change your current directory. Then, install all necessary dependencies:
```bash
git clone kudashevs/birthday-greetings-kata-php
cd birthday-greetings-kata-php
composer install
```

Run docker compose. Then, run tests:
```bash
docker-composer up -d
vendor/bin/phpunit
```
You can check received emails in your browser at http://localhost:1080


## Project Structure

- `src/birthday_greetings/` - Main application code
- `tests/` - Test code
- `employee_data.txt` - Sample employee data file


## The Exercise

The goal is to refactor the code to:
- Apply dependency inversion principle
- Implement dependency injection
- Separate concerns
- Make the code more testable and maintainable


## Notes

- The tests use MailHog as a fake SMTP server, which provides a web interface to inspect received emails
- The code intentionally contains code smells that should be addressed during the refactoring exercise
