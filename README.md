# Kairos_System

## Context
Central to the ethos of my high school (Terrace) is the "Retreat" program – a special part of the school's faith and mission that sees boys engage formally in deep reflection. At Terrace there are many retreat programs but focus of this project is detailed around organisation of Kairos (our Year 12 optional retreat program) that is run 4 times a year. Currently, the school uses a mixture of spreadsheets, Google forms and other separate components, often leading to inconvenient loss of data and unstructured management of the program. This proposed web system hopes to combine the existing componenets into one system to rule them all. There are three types of users: students, organisers and administrator.

## Student
Students in this system register an account (factor authentication via email verification), nominate which retreat they are interested in attending, and a second preference if their first choice is not available. If required, students are able to reques for a resubmission of their preferences. They must then get parental permission and follow through with this commitment.

## Organiser
Organisers organise events and proceedings at the retreat. They monitor student participation, mark rolls, annotate student experiences and have access to most of the student profile information (in read-only form) – as such, organisers will be a Terrace staff or authorised Blue Card holders. Safety of students and their data is important. Organisers can also view information about each retreat (including enrolled numbers and remaining positions), as well as the list of students attending a specific Kairos grouped into houses or tutor groups.

## Admin
Administrators manage user data including the addition of Organisers. Additionally, they can see/change any aspect of the stored data via a SQL database engine.
