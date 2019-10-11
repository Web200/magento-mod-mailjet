Mailjet Sync (Magento 2.3)

Features 
========

Api
---
* Get mailjet template list (Marketing / Automation)
* Send Email

Api Use 
--- 
Send email

```php
use Web200\Mailjet\Model\Webservice\Email as MailjetEmail;

class Test {
    public function __construct(
        MailjetEmail $mailjetEmail
    ) {
        $this->mailjetEmail = $mailjetEmail;
    }

    public function send()
    {
        $this->mailjetEmail->setFromEmail('sender@example.com');
        $this->mailjetEmail->setFromName('From Name');
        $to = [
            'Email' => 'recipient@example.com',
            'Name' => 'Recipient Name'
        ];
        $this->mailjetEmail->setVariables(['var1' => 'test']);
        $this->mailjetEmail->setTo($to);
        $this->mailjetEmail->setTemplateId((int)$mailjetTemplateId);
        $this->mailjetEmail->send();
    }
}
```

Others
---
* Sync Mail subscription / unsubscription with MailJet
* Mailjet properties *firstname* *lastname* and *dob* is sent when synchronisation (You need to create properties in MailJet)
* Add Firstname / Lastname / Dob in Admin subscription grid
* Add Firstname / Lastname / Dob in Magento\Newsletter\Model\Subscriber
* Send mail through mailjet api if mailjet template choose.

You can save guest subscription :

```php
$factory = $this->subscriberFactory->create();
$factory->setSubscriberLastname($lastname);
$factory->setSubscriberFirstname($firstname);
$factory->setSubscriberDob($dob);
$factory->subscribe($email);
```
