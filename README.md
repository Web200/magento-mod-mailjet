Mailjet Sync

Features 
========
* Sync Mail subscription / unsubscription with MailJet
* Mailjet properties *firstname* *lastname* and *dob* is sent when synchronisation (You need to create properties in MailJet)
* Add Firstname / Lastname / Dob in Admin subscription grid
* Add Firstname / Lastname / Dob in Magento\Newsletter\Model\Subscriber

You can save guest subscription :

```php
$factory = $this->subscriberFactory->create();
$factory->setSubscriberLastname($lastname);
$factory->setSubscriberFirstname($firstname);
$factory->setSubscriberDob($dob);
$factory->subscribe($email);
```
