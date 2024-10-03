# Installation

TODO


## Overview

Archive jobs are execute via an external quiz archive worker service. It uses the
Moodle webservice API to query the required data and to upload the created archive.

This plugin prepares the archive job within Moodle, provides quiz data to the
archive worker, handles data validation, and stores the created quiz archives
inside the Moodle filestore. Created archives can be managed and downloaded via
the Moodle web interface. A unique webservice access token is generated for every
archive job. Each token has a limited validity and is invalidated either after
job completion or after a specified timeout. This process requires a dedicated
webservice user to be created (see [Configuration](/configuration)). A single job
webservice token can only be used for the specific quiz that is associated with
the job to restrict queryable data to the required minimum.


## Requirements

TODO


## Versioning and Compatibility

The [quiz_archiver Moodle Plugin](https://github.com/ngandrass/moodle-quiz_archiver)
and its corresponding [Quiz Archive Worker](https://github.com/ngandrass/moodle-quiz-archive-worker)
both use [Semantic Versioning 2.0.0](https://semver.org/).

This means that their version numbers are structured as `MAJOR.MINOR.PATCH`. The
Moodle plugin and the archive worker service are compatible as long as they use
the same `MAJOR` version number. Minor and patch versions can differ between the
two components without breaking compatibility.

However, it is **recommended to always use the latest version** of both the
Moodle plugin and the archive worker service to ensure you get all the latest
bug fixes, features, and optimizations.


### Compatibility Examples

| Moodle Plugin | Archive Worker | Compatible |
|------------|----------------|------------|
| 1.0.0      | 1.0.0          | Yes        |
| 1.2.3      | 1.0.0          | Yes        |
| 1.0.0      | 1.1.2          | Yes        |
| 2.1.4      | 2.0.1          | Yes        |
|            |                |            |
| 2.0.0      | 1.0.0          | No         |
| 1.0.0      | 2.0.0          | No         |
| 2.4.2      | 1.4.2          | No         |


### Development / Testing Versions

Special development versions, used for testing, can be created but will never be
published to the Moodle plugin directory. Such development versions are marked
by a `+dev-[TIMESTAMP]` suffix, e.g., `2.4.2+dev-2022010100`.

