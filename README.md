# wp-plugin-test

Wordpress plugin test for KeyPress Media LLC company.

Adds a new item under the admin menu called "Resume" which links to a custom options page.
- The options page contains a form with the following fields:
      + Name (Text field)
      + Resume (Textarea) 
      + Send copy (Checkbox)
- When checking the "Send copy" field, a new text field for an email address will appear.
- When the form is submitted, its fields are saved in the Wordpress options table and if the "Send copy" field has been checked, a copy is sent to the email address entered.
