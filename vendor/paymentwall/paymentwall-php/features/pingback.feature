Feature: Pingback
  In order to account for Paymentwall payments on my website
  As a developer
  I want to be able to validate Paymentwall pingbacks


Scenario: check Digital Goods pingback signature v2 with correct signature and correct IP
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "a7408723eaf4bfa2e3ac49b3cb695046"
  And API type "2"
  And Pingback GET parameters "uid=test_user&goodsid=test_product&slength=5&speriod=month&type=0&ref=t123&is_test=1&sign_version=2&sig=754cff93c0eb859f6054bef143ad253c"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "true"
  And Pingback method "getUserId" should return "test_user"
  And Pingback method "getProductId" should return "test_product"
  And Pingback method "getProductPeriodLength" should return "5"
  And Pingback method "getProductPeriodType" should return "month"
  And Pingback method "getReferenceId" should return "t123"
  And Pingback method "isDeliverable" should return "true"
  And Pingback method "isCancelable" should return "false"


Scenario: check Digital Goods pingback signature v2 with correct signature and wrong IP
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "a7408723eaf4bfa2e3ac49b3cb695046"
  And API type "2"
  And Pingback GET parameters "uid=test_user&goodsid=test_product&slength=5&speriod=month&type=0&ref=t123&is_test=1&sign_version=2&sig=754cff93c0eb859f6054bef143ad253c"
  And Pingback IP address "1.2.3.4"
  When Pingback is constructed
  Then Pingback validation result should be "false"


Scenario: check Digital Goods pingback signature v2 with wrong signature and correct IP
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "a7408723eaf4bfa2e3ac49b3cb695046"
  And API type "2"
  And Pingback GET parameters "uid=test_user&goodsid=test_product&slength=5&speriod=month&type=0&ref=t123&is_test=1&sign_version=2&sig=754cff93c0eb859f6054bef143ad253cfoo"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "false"


Scenario: check Digital Goods negative pingback signature v3 with correct signature and correct IP
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "a7408723eaf4bfa2e3ac49b3cb695046"
  And API type "2"
  And Pingback GET parameters "uid=test_user&goodsid=test_product&slength=-5&speriod=month&type=2&ref=t123&is_test=1&reason=9&sign_version=3&sig=2f67209c3e581313a70de9425efef49f35a74c0cdb7f93051b47e3c097011a71"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "true"
  And Pingback method "getProductPeriodLength" should return "-5"
  And Pingback method "getProductPeriodType" should return "month"
  And Pingback method "isDeliverable" should return "false"
  And Pingback method "isCancelable" should return "true"


Scenario: check Digital Goods negative pingback signature v1 with correct signature
  Given Public key "c22f895840bf2391f67a40da64bfed26"
  And Secret key "a7408723eaf4bfa2e3ac49b3cb695046"
  And API type "2"
  And Pingback GET parameters "uid=test_user&goodsid=test_product&slength=-5&speriod=month&type=2&ref=t123&is_test=1&reason=9&sig=e7b2ff07bc0734c83ee14f32552b1c88"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "true"


Scenario: check Digital Goods pingback signature v1 with correct signature
  Given Public key "c10c60d07a2f4549a17902d683eb0b11"
  And Secret key "6274def95b105f1c92d341a8d3bc2e77"
  And API type "1"
  And Pingback GET parameters "uid=test_user&currency=1000&type=0&ref=t555&is_test=1&sig=efaacb488ab8ee19321ad513b6908574"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "true"
  And Pingback method "getVirtualCurrencyAmount" should return "1000"


Scenario: check Virtual Currency pingback signature v1 with wrong signature
  Given Public key "c10c60d07a2f4549a17902d683eb0b11"
  And Secret key "6274def95b105f1c92d341a8d3bc2e77"
  And API type "1"
  And Pingback GET parameters "uid=test_user&currency=1000&type=0&ref=t555&is_test=1&sig=efaacb488ab8ee19321ad513b6908574foo"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "false"

Scenario: check Digital Goods pingback signature v2 with correct signature
  Given Public key "c10c60d07a2f4549a17902d683eb0b11"
  And Secret key "6274def95b105f1c92d341a8d3bc2e77"
  And API type "1"
  And Pingback GET parameters "uid=test_user&currency=1000&type=0&ref=t555&is_test=1&sign_version=2&sig=5057977f881bed13592bec928f062b31"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "true"
  And Pingback method "getVirtualCurrencyAmount" should return "1000"


Scenario: check Virtual Currency pingback signature v2 with wrong signature
  Given Public key "c10c60d07a2f4549a17902d683eb0b11"
  And Secret key "6274def95b105f1c92d341a8d3bc2e77"
  And API type "1"
  And Pingback GET parameters "uid=test_user&currency=1000&type=0&ref=t555&is_test=1&sign_version=2&sig=5057977f881bed13592bec928f062b31foo"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "false"


Scenario: check Digital Goods pingback signature v3 with correct signature
  Given Public key "c10c60d07a2f4549a17902d683eb0b11"
  And Secret key "6274def95b105f1c92d341a8d3bc2e77"
  And API type "1"
  And Pingback GET parameters "uid=test_user&currency=1000&type=0&ref=t555&is_test=1&sign_version=3&sig=a2932c360010e613166ae95ede5a3fa45bfcac10e1dd93715d21b00d684eb0fb"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "true"
  And Pingback method "getVirtualCurrencyAmount" should return "1000"


Scenario: check Virtual Currency pingback signature v3 with wrong signature
  Given Public key "c10c60d07a2f4549a17902d683eb0b11"
  And Secret key "6274def95b105f1c92d341a8d3bc2e77"
  And API type "1"
  And Pingback GET parameters "uid=test_user&currency=1000&type=0&ref=t555&is_test=1&sign_version=3&sig=a2932c360010e613166ae95ede5a3fa45bfcac10e1dd93715d21b00d684eb0fbfoo"
  And Pingback IP address "174.36.92.186"
  When Pingback is constructed
  Then Pingback validation result should be "false"

