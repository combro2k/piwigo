<!-- DEV TAG: not smarty migrated -->
<!-- $Id$ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_LOST_PASSWORD}" title="{lang:Forgot your password?}"><img src="{themeconf:icon_dir}/lost_password.png" class="button" alt="{lang:Forgot your password?}"></a></li>
<!-- BEGIN register -->
      <li><a href="{U_REGISTER}" title="{lang:Create a new account}"><img src="{themeconf:icon_dir}/register.png" class="button" alt="{lang:register}"/></a></li>
<!-- END register -->
      <li><a href="{U_HOME}" title="{lang:Go through the gallery as a visitor}"><img src="{themeconf:icon_dir}/home.png" class="button" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Identification}</h2>
  </div>

  <!-- BEGIN errors -->
  <div class="errors">
    <ul>
      <!-- BEGIN error -->
      <li>{errors.error.ERROR}</li>
      <!-- END error -->
    </ul>
  </div>
  <!-- END errors -->

<form action="{F_LOGIN_ACTION}" method="post" name="login_form" class="properties">
  <fieldset>
    <legend>{lang:Connection settings}</legend>

    <input type="hidden" name="redirect" value="{U_REDIRECT}">

    <ul>
      <li>
        <span class="property">
          <label for="username">{lang:Username}</label>
        </span>
        <input tabindex="1" class="login" type="text" name="username" id="username" size="25" maxlength="40" value="{USERNAME}" />
      </li>

      <li>
        <span class="property">
          <label for="password">{lang:Password}</label>
        </span>
        <input tabindex="2" class="login" type="password" name="password" id="password" size="25" maxlength="25" />
      </li>

      <!-- BEGIN remember_me -->
      <li>
        <span class="property">
          <label for="remember_me">{lang:remember_me}</label>
        </span>
        <input tabindex="3" type="checkbox" name="remember_me" id="remember_me" value="1">
      </li>
      <!-- END remember_me -->
    </ul>
  </fieldset>

  <p><input class="submit" tabindex="4" type="submit" name="login" value="{lang:submit}"></p>
</form>

<script type="text/javascript"><!--
document.login_form.username.focus();
//--></script>

  <p>
<!-- BEGIN register -->
    <a href="{U_REGISTER}" title="{lang:Create a new account}"><img src="{themeconf:icon_dir}/register.png" class="button" alt=""> {lang:Register}</a>
<!-- END register -->
    <a href="{U_LOST_PASSWORD}" title="{lang:Forgot your password?}"><img src="{themeconf:icon_dir}/lost_password.png" class="button" alt=""> {lang:Forgot your password?}</a>
  </p>

</div> <!-- content -->
