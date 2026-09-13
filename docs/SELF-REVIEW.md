# Self-review

## Errors I found

There were not many real errors. Most of them were my own small mistakes and typo, a file I forgot to save before running the app, volume put under the wrong service in `docker-compose.yml` and they were all quick to fix, because the error messages showed exactly which file and line to look at.

## Assumptions

I chose to build G1 in Docker because I wanted to strengthen my skills in Docker, and this task was a good chance to set it up myself instead of using a ready environment. The task did not mention users, so I assumed no login is needed and everyone who opens the app can see and change everything. I used plain PHP instead of a framework, so that every part of the app is visible and easy to explain.

## Weak points

- No automated tests – everything was tested by hand.
- No login, no pagination, no search.
- Didnt teste on mobile.
- I am still new to Docker.

## Next steps

Next steps- automated tests for the validation, a proper login so only the owner can change a project, pagination and search. If this were a real application that has to grow in the future, I would probably build it in Laravel, because it would be easier to expand on it.