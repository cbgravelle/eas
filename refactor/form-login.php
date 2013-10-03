              <p>
              <form action="/login<?php echo $redirect; ?>" method="post">
                <input type="hidden" name="login" value="1">
                <p>
                  <input type="text" name="username" placeholder="username or email address">
                </p>
                <p>
                  <input type="password" name="password" placeholder="password">
                </p>
                <p>
                  <label for="remember" class="checkbox"><input type="checkbox" name="remember">Remember Me</label>
                </p>
                <p>
                  <input type="submit" class="btn btn-primary" value="Login">
                </p>
                <p>
                  <a href="/forgotpassword" title="Password Recovery">Forgot your Password?</a>
                </p>
              </form>