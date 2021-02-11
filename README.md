# MentionNotifications
Mention user notifications for Mentions UI component.

This component is a tool that sends notification when user is mentioned in posts. This component requires that MentionUsers to be disabled (provided by bryce) because that component didn't work as intended it works with names and send notification to all users with such a name. Whereas Mention UI intended to tag user only the person selected in the list.

The component requires https://www.opensource-socialnetwork.org/component/view/4844/mentions-ui

This send notification only if person is tagged in comments of following:

- Wall post created using newsfeed/profile.
- Profile photos.
- Profile covers.
- Album photos.
