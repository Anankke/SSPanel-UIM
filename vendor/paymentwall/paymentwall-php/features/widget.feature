Feature: Widget
  In order to let users make payments on my website
  As a developer
  I want to be able to generate HTML code of Paymentwall widgets

Scenario: check Digital Goods widget signature v2 with correct secret key
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "a7408723eaf4bfa2e3ac49b3cb695046"
  And API type "2"
  And Widget signature version "2" 
  And Product name "Automatic Test Product Name"
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget HTML content should not contain "It looks like you're not authorized to view this content."
  And Widget HTML content should contain "Automatic Test Product Name"

Scenario: check Digital Goods widget signature v2 with wrong secret key
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "000"
  And API type "2"
  And Widget signature version "2" 
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget HTML content should contain "It looks like you're not authorized to view this content."

Scenario: check Digital Goods widget signature v1 with correct secret key
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "a7408723eaf4bfa2e3ac49b3cb695046"
  And API type "2"
  And Widget signature version "1" 
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget HTML content should not contain "It looks like you're not authorized to view this content."

Scenario: check Digital Goods widget signature v1 with wrong secret key
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "000"
  And API type "2"
  And Widget signature version "1" 
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget HTML content should contain "It looks like you're not authorized to view this content."

Scenario: check Digital Goods widget signature v3 with correct secret key
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "a7408723eaf4bfa2e3ac49b3cb695046"
  And API type "2"
  And Widget signature version "3" 
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget HTML content should not contain "It looks like you're not authorized to view this content."

Scenario: check Digital Goods widget signature v3 with wrong secret key
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "000"
  And API type "2"
  And Widget signature version "3" 
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget HTML content should contain "It looks like you're not authorized to view this content."

Scenario: check Digital Goods widget signature v3 with wrong secret key
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "a7408723eaf4bfa2e3ac49b3cb695046"
  And API type "2"
  And Widget signature version "3" 
  And Product name "Automatic Test Product Name"
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget HTML content should not contain "It looks like you're not authorized to view this content."
  And Widget HTML content should contain "Automatic Test Product Name"

Scenario: check Virtual Currency offer widget signature v2 with correct secret key
  Given Public key "c10c60d07a2f4549a17902d683eb0b11"
  And Secret key "6274def95b105f1c92d341a8d3bc2e77"
  And API type "1"
  And Widget signature version "2" 
  And Widget code "w1" 
  And Language code "en" 
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget URL should contain "/api/?"
  And Widget HTML content should not contain "It looks like you're not authorized to view this content."
  And Widget HTML content should contain "by completing offers below"

Scenario: check Virtual Currency payment widget signature v2 with correct secret key
  Given Public key "c10c60d07a2f4549a17902d683eb0b11"
  And Secret key "6274def95b105f1c92d341a8d3bc2e77"
  And API type "1"
  And Widget signature version "2"
  And Widget code "p10" 
  And Language code "en" 
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget URL should contain "/api/ps?"
  And Widget HTML content should not contain "It looks like you're not authorized to view this content."
  And Widget HTML content should contain "Select payment method"


Scenario: check Virtual Currency widget signature v2 with wrong secret key
  Given Public key "c10c60d07a2f4549a17902d683eb0b11"
  And Secret key "000"
  And API type "1"
  And Widget signature version "2"
  When Widget is constructed 
  And Widget HTML content is loaded
  Then Widget HTML content should contain "It looks like you're not authorized to view this content."