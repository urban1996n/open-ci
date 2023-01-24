## Continuous integration system.
### By way of introduction

The subject of following document is the plan of continuous integration system implementation.  
The document should be divided into x sections describing basic assumptions, requirements and milestones to be fulfilled by previously fixed due date of 01/01/2023.

### Basic assumptions and findings:
1. To keep the deadline some functionalities may not be presumed final, and/or may not be fully functional.
2. This document and results of must be reviewed and updated after completing a milestone, or as a result of problems encountered while working on one.
3. Each milestone completion must be documented to make following project's advances possible.
4. The CI project will be kept on personal public repository.
5. The project will be acknowledged as completed after all milestones are completed, despite final implementation.
6. The following roadmap/milestones are not final and will be updated after making progress in the project completion (especially after finishing researches described in the milestones).

### Roadmap/Milestones for CI system introduction.
1. Create specific architecture graph of the CI system communication and processes.
2. Specify communication protocols and abstract communication layers
3. Specify technologies and libraries to be used for CI system implementation. __Edited: (composer.json file in lib directory)__
4. Write down specific implementation steps to be treated as another milestones.
5. Create pipeline schema to make sure it will be kept intact for further processing [pipeline_schema](./schemes/pipeline/schema/json-schema.json)
6. Create first app components to be run with.
7. Create pipeline runner and artifact (logs) processor.
8. Create command for job execution.
