# Weather Service API, version 1

Every request needs the header X-API-Key.
Base path: /api/v1

Times in fetchedAt are in UTC. Temperatures are in degrees Celsius.

All errors use the same shape:
```json
{ "status": 422, "error": { "code": "invalid_limit", "message": "limit must be 1 to 50." } }
```

## POST /weather/refresh
What it does: Gets the current temperature for Iligan City from the Open-Meteo weather service and saves it as a new reading.

Success response: 201. The saved reading.
```json
{
  "status": 201,
  "data": { "id": 5, "city": "Iligan City", "temperatureC": 25.1, "fetchedAt": "2026-10-06T19:01:20" }
}
```

Errors:
- 401 unauthorized: the X-API-Key header is missing or wrong.
- 502 upstream_error: the weather source could not be reached, or it sent no temperature. Nothing is saved.

## GET /weather/logs
What it does: Lists saved readings, newest first.

Options:
- limit: how many readings to return, from 1 to 50. The default is 10.
- format: json or xml. The default is json.

Success response: 200. A list of readings.
```json
{
  "status": 200,
  "data": [
    { "id": 5, "city": "Iligan City", "temperatureC": 25.1, "fetchedAt": "2026-10-06T19:01:20" },
    { "id": 4, "city": "Iligan City", "temperatureC": 25.3, "fetchedAt": "2026-09-21T16:53:02" }
  ]
}
```
With format=xml, the response is XML with a weatherReport element that holds one reading element for each reading.

Errors:
- 401 unauthorized: the X-API-Key header is missing or wrong.
- 422 invalid_limit: limit is not a whole number from 1 to 50.
- 422 invalid_format: format is not json or xml.
- 500 invalid_xml: the XML did not match the schema, so it was not sent.

## GET /weather/logs/{id}
What it does: Returns one saved reading by its id number.

Success response: 200. One reading.
```json
{
  "status": 200,
  "data": { "id": 1, "city": "Iligan City", "temperatureC": 25.2, "fetchedAt": "2026-09-21T16:03:00" }
}
```

Errors:
- 401 unauthorized: the X-API-Key header is missing or wrong.
- 404 not_found: no reading has that id.
