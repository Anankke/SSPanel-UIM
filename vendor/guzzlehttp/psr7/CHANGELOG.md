# CHANGELOG

## 1.3.1 - 2016-06-25

* Fix `Uri::__toString` for network path references, e.g. `//example.org`.
* Fix missing lowercase normalization for host.
* Fix handling of URI components in case they are `'0'` in a lot of places,
  e.g. as a user info password.
* Fix `Uri::withAddedHeader` to correctly merge headers with different case.
* Fix trimming of header values in `Uri::withAddedHeader`. Header values may
  be surrounded by whitespace which should be ignored according to RFC 7230
  Section 3.2.4. This does not apply to header names.
* Fix `Uri::withAddedHeader` with an array of header values.
* Fix `Uri::resolve` when base path has no slash and handling of fragment.
* Fix handling of encoding in `Uri::with(out)QueryValue` so one can pass the
  key/value both in encoded as well as decoded form to those methods. This is
  consistent with withPath, withQuery etc.
* Fix `ServerRequest::withoutAttribute` when attribute value is null.

## 1.3.0 - 2016-04-13

* Added remaining interfaces needed for full PSR7 compatibility
  (ServerRequestInterface, UploadedFileInterface, etc.).
* Added support for stream_for from scalars.
* Can now extend Uri.
* Fixed a bug in validating request methods by making it more permissive.

## 1.2.3 - 2016-02-18

* Fixed support in `GuzzleHttp\Psr7\CachingStream` for seeking forward on remote
  streams, which can sometimes return fewer bytes than requested with `fread`.
* Fixed handling of gzipped responses with FNAME headers.

## 1.2.2 - 2016-01-22

* Added support for URIs without any authority.
* Added support for HTTP 451 'Unavailable For Legal Reasons.'
* Added support for using '0' as a filename.
* Added support for including non-standard ports in Host headers.

## 1.2.1 - 2015-11-02

* Now supporting negative offsets when seeking to SEEK_END.

## 1.2.0 - 2015-08-15

* Body as `"0"` is now properly added to a response.
* Now allowing forward seeking in CachingStream.
* Now properly parsing HTTP requests that contain proxy targets in
  `parse_request`.
* functions.php is now conditionally required.
* user-info is no longer dropped when resolving URIs.

## 1.1.0 - 2015-06-24

* URIs can now be relative.
* `multipart/form-data` headers are now overridden case-insensitively.
* URI paths no longer encode the following characters because they are allowed
  in URIs: "(", ")", "*", "!", "'"
* A port is no longer added to a URI when the scheme is missing and no port is
  present.

## 1.0.0 - 2015-05-19

Initial release.

Currently unsupported:

- `Psr\Http\Message\ServerRequestInterface`
- `Psr\Http\Message\UploadedFileInterface`
