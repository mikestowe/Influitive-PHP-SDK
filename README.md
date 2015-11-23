# Influitive-PHP-SDK
Simple SDK for Using the Influitive API to retrieve member information, events, or approve (corporate workflow) challenges.

```
<?php
require_once('members.php');
require_once('events.php');
require_once('approvals.php');

// Get Tom's information by his ID
$tom = members::getById(1);

// Get Tom's information by his email
$tom = members::getByEmail('tom@tomtom.tom');

// Get latest badges earned
$badges = events::getByType('earned_badge');

// Note getByMemberId doesn't work correctly yet due to limitations with their API

// Get All Pending Corporate Challenges (waiting for approval)
$allChallenges = approvals::getAll();

// Get all pending approvals by specific challenge ID
$specificChallenges = approvals::getByChallengeId(10);

// Approve a challenge (after getting its ID from the events::getAll() or ByChallengeID)
events::approve(454);

// Reject a challenge
events::reject(454);

// Only provide feedback
events::feedback_only(454, "this is my feedback");
```

More features will be added down the road, but hopefully this is useful.  Feel free to contribute classes as well :)
