<?php
/*
    Plugin Name: Resume Plugin test
    Description: Save user name, resume and send copy to email
    Author: Victor Samayoa
    email: vhs1092@gmail.com
    Version: 1.0.0
*/
class Resume_Victor_Samayoa_Plugin {

    /*
    * Register plugin in admin menu
    */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'create_plugin' ) );
    }

    /*
    * Creating plugin
    */
    public function create_plugin() {
        // Add the menu item and page
        $page_title = 'My plugin test';
        $menu_title = 'My plugin test';
        $capability = 'manage_options';
        $slug = 'resume_plugin';
        $callback = array( $this, 'plugin_content' );
        $icon = 'dashicons-admin-plugins';
        $position = 90;

        //adding menu
        add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }

    /*
    * Html content
    * Fields (name, resume, send_copy)
    */
    public function plugin_content() {
        if( $_POST['updated'] === 'true' ){
            $this->handle_form();
        } ?>

        <div class="wrap">
            <h2>My Wp Plugin</h2>
            <form method="POST">
                <input type="hidden" name="updated" value="true" />
                <?php wp_nonce_field( 'resume_update', 'resume_form' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th><label for="name">Name</label></th>
                            <td><input name="name" id="name" type="text" value="<?php echo get_option('resume_name'); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="name">Resume</label></th>
                            <td><textarea name="resume" rows="4" cols="50" required><?php echo get_option('resume_text'); ?></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="name">Send copy</label></th>
                            <td><input type="checkbox" id="send_copy" onclick="check_copy();" name="send_copy" value="true"></textarea></td>
                        </tr>
                        <tr class="email" style="display: none">
                            <th><label for="email">Email Address</label></th>
                            <td><input name="email" id="email" type="text" value="<?php echo get_option('resume_email'); ?>" class="regular-text" /></td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Send!">
                </p>
            </form>
        </div> 
        <script src="<?php echo includes_url();?>js/jquery/jquery.js"></script>
        <script type="text/javascript">
            /*
            * Shows email field if send copy is checked
            */
            function check_copy() {
                if (jQuery("#send_copy").is(':checked')) {
                    jQuery(".email").show();
                } else {
                    jQuery(".email").hide();
                }
            }

            jQuery(document).ready(function(){
                var send_copy = '<?php echo get_option('resume_copy')?>';
                if (send_copy == 'true') {
                    jQuery("#send_copy").prop("checked", true);
                    jQuery(".email").show();
                }
            });
        </script>
        <?php
    }

    /*
    * Handle form after submit
    */
    public function handle_form() {
        if( ! isset( $_POST['resume_form'] ) || ! wp_verify_nonce( $_POST['resume_form'], 'resume_update' ) ){ ?>
           <div class="error">
               <p>Sorry, your nonce was not correct. Please try again.</p>
           </div> <?php
           exit;
        } else {
            // sanitize values
            $name = sanitize_text_field( $_POST['name'] );
            $resume = sanitize_text_field( $_POST['resume'] );
            $send_copy = sanitize_text_field( $_POST['send_copy'] );
            $validate_email = true;
            
            if(isset($_POST['email'])){
                //if email, validate it
                $email = sanitize_text_field( $_POST['email'] );
                $validate_email = $this->validate_email($email);
            }
            
            if(!$validate_email){ // wrong email format
                ?>
                <div class="error">
                    <p>Your email is invalid.</p>
                </div>
            <?php }else{
             //inputs ok save them   
             update_option( 'resume_name', $name );
             update_option( 'resume_text', $resume );
             update_option( 'resume_copy', $send_copy );
             update_option( 'resume_email', $email );
             
             if($send_copy == 'true'){
                //if send copy is checked and email is correct send email
                $data["name"] = $name;
                $data["text"] = $resume;
                $data["email"] = $email;
                
                $this->send_email($data);

             }

             ?>
                <div class="updated">
                    <p>Your fields were saved!</p>
                </div>   
            <?php }    
            
        }
    }
    /*
    * Validate email field
    */
    public function validate_email($email){
        
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          return true;
        } else {
          return false;
        }
    }

    /*
    * Send email with wp_email function
    */
    public function send_email($data){

       $to = $data["email"];
       $message = '
       <table style="text-align:left">
         <thead>
            <th style="padding: 10px;
            border-right: 1px solid #bcbcbc; border-bottom: 1px solid #bcbcbc;">Name</th>
            <th style="padding: 10px; border-bottom: 1px solid #bcbcbc;">Resume</th>
         </thead>
         <tbody>
            <tr>
            <td style="padding: 10px;
            border-right: 1px solid #bcbcbc;">'.$data["name"].'</td>    
            <td style="padding: 10px;">'.$data["text"].'</td>
            </tr>    
        <tbody>
       </table>';

       //convert content in html
       add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
       wp_mail($to, 'New Resume Received', $message);

    }
}
new Resume_Victor_Samayoa_Plugin();
