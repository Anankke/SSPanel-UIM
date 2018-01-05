Feature: Charge
  In order to let users make payments on my website
  As a developer
  I want to be able to perform a charge using Brick

Scenario: Should be able to create a test charge
  Given Public key "t_33c1806e0daf60fc31f2167f0e4d59"
  And Private key "t_85c66c2d7461e8885805f92dfd171c"
  When test token is retrieved
  Then charge should be successful

Scenario: Should be able to refund a test charge
  Given Public key "t_33c1806e0daf60fc31f2167f0e4d59"
  And Private key "t_85c66c2d7461e8885805f92dfd171c"
  And charge ID "9557984691424639727_test"
  Then charge should be refunded

Scenario: Should not be able to create a test charge with wrong CVV code
  Given Public key "t_33c1806e0daf60fc31f2167f0e4d59"
  And Private key "t_85c66c2d7461e8885805f92dfd171c"
  And CVV code "333"
  When test token is retrieved
  Then I see this error message "Please contact your credit card company to approve your payment"