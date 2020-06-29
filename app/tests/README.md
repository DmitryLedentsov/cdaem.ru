Putting It All Together

Now we've seen the basics, we can start combining these rules.

A popular UNIX/Linux service is the secure shell (SSH) service allowing remote logins. By default SSH uses port 22 and again uses the tcp protocol. So if we want to allow remote logins, we would need to allow tcp connections on port 22:

# Accept tcp packets on destination port 22 (SSH)
 iptables -A INPUT -p tcp --dport 22 -j ACCEPT

This will open up port 22 (SSH) to all incoming tcp connections which poses a potential security threat as hackers could try brute force cracking on accounts with weak passwords. However, if we know the IP addresses of trusted remote machines that will be used to log on using SSH, we can limit access to only these source IP addresses. For example, if we just wanted to open up SSH access on our private lan (192.168.0.x), we can limit access to just this source IP address range:

# Accept tcp packets on destination port 22 (SSH) from private LAN
 iptables -A INPUT -p tcp -s 192.168.0.0/24 --dport 22 -j ACCEPT

Using source IP filtering allows us to securely open up SSH access on port 22 to only trusted IP addresses. For example, we could use this method to allow remote logins between work and home machines. To all other IP addresses, the port (and service) would appear closed as if the service were disabled so hackers using port scanning methods are likely to pass us by. This directory contains various tests for the advanced applications.

Tests in `codeception` directory are developed with [Codeception PHP Testing Framework](http://codeception.com/).

After creating and setting up the advanced application, follow these steps to prepare for the tests:

1. Install Codeception if it's not yet installed:

   ```
   composer global require "codeception/codeception=2.0.*" "codeception/specify=*" "codeception/verify=*"
   ```

   If you've never used Composer for global packages run `composer global status`. It should output:

   ```
   Changed current directory to <directory>
   ```

   Then add `<directory>/vendor/bin` to you `PATH` environment variable. Now you're able to use `codecept` from command
   line globally.

2. Install faker extension by running the following from template root directory where `composer.json` is:

   ```
   composer require --dev yiisoft/yii2-faker:*
   ```

3. Create `yii2_advanced_tests` database then update it by applying migrations:

   ```
   codeception/bin/yii migrate
   ```

4. In order to be able to run acceptance tests you need to start a webserver. The simplest way is to use PHP built in
   webserver. In the root directory where `common`, `frontend` etc. are execute the following:

   ```
   php -S localhost:8080
   ```

5. Now you can run the tests with the following commands, assuming you are in the `tests/codeception` directory:

   ```
   # frontend tests
   cd frontend
   codecept build
   codecept run
   
   # backend tests
   
   cd backend
   codecept build
   codecept run
    
   # etc.
   ```

  If you already have run `codecept build` for each application, you can skip that step and run all tests by a single `codecept run`.
