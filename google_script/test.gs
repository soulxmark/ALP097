function testOTP() {
  GmailApp.sendEmail(
    'mark2899@yahoo.com',  // your email
    'Test OTP',
    '',
    { htmlBody: '<h1>Test - your OTP is: 123456</h1>' }
  );
}
//Testing for email send 