## 1.7 (2014-1-30)

Bugfixes:
  - patched bug for attachments related to duplicate aggregator bug in Guzzle (#32 @travelton)

## 1.6 (2014-1-13)

Enhancement:
  - adjust file attachment/inline name (#21 @travelton)

Bugfixes:
  - fixed issue with unordered route actions (#23 @travelton)

## 1.5 (2013-12-13)

Enhancement:
  - added ability to define non-https endpoint for debugging purposes (#23 @travelton)

## 1.4 (2013-10-16)

Bugfixes:
  - template IDs were missing from recipient-variables (#15 @travelton)
  - batch jobs trigger on to, cc, and bcc (#18 @travelton)
  - batch jobs include recipient-variables for to, cc, and bcc (#18 @travelton)
  - added method to return message-ids, for easier access (#19 @travelton)

## 1.3 (2013-09-12)

Bugfixes:
  
  - relaxed Guzzle requirement (#7 @travelton)
  - fixed reply-to bug (#9 @travelton)

## 1.2 (2013-09-05)

Bugfixes:

  - fixed exception handling constants (@travelton)
  - fixed MessageBuilder $baseAddress return (#1 @yoye)
  - adjusted scope of recipient-variables (#3 @yoye)
  - fixed misspellings of Exceptions (#2 @dboggus)
  - undefined DEFAULT_TIME_ZONE (#4 @yoye)
  - added message IDs to return for BatchMessage (@travelton)

## 1.1 (2013-08-21)

Initial Release!
