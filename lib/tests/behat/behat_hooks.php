<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Behat hooks steps definitions.
 *
 * This methods are used by Behat CLI command.
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Testwork\Hook\Scope\BeforeSuiteScope,
    Behat\Testwork\Hook\Scope\AfterSuiteScope,
    Behat\Behat\Hook\Scope\BeforeFeatureScope,
    Behat\Behat\Hook\Scope\AfterFeatureScope,
    Behat\Behat\Hook\Scope\BeforeScenarioScope,
    Behat\Behat\Hook\Scope\AfterScenarioScope,
    Behat\Behat\Hook\Scope\BeforeStepScope,
    Behat\Behat\Hook\Scope\AfterStepScope,
    Behat\Mink\Exception\DriverException as DriverException,
    WebDriver\Exception\NoSuchWindow as NoSuchWindow,
    WebDriver\Exception\UnexpectedAlertOpen as UnexpectedAlertOpen,
    WebDriver\Exception\UnknownError as UnknownError,
    WebDriver\Exception\CurlExec as CurlExec,
    WebDriver\Exception\NoAlertOpenError as NoAlertOpenError;

/**
 * Hooks to the behat process.
 *
 * Behat accepts hooks after and before each
 * suite, feature, scenario and step.
 *
 * They can not call other steps as part of their process
 * like regular steps definitions does.
 *
 * Throws generic Exception because they are captured by Behat.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_hooks extends behat_base {

    /** @var bool Totara: Force restart before the next scenario */
    public static $forcerestart = false;

    /**
     * @var Last browser session start time.
     */
    protected static $lastbrowsersessionstart = 0;

    /**
     * @var For actions that should only run once.
     */
    protected static $initprocessesfinished = false;

    /**
     * If we are saving any kind of dump on failure we should use the same parent dir during a run.
     *
     * @var The parent dir name
     */
    protected static $faildumpdirname = false;

    /**
     * Keeps track of time taken by feature to execute.
     *
     * @var array list of feature timings
     */
    protected static $timings = array();

    /**
     * Keeps track of current running suite name.
     *
     * @var string current running suite name
     */
    protected static $runningsuite = '';

    /**
     * Send log message to error_log.
     *
     * Note: the main purpose of the method is to fix timezone in logs to match php.ini
     *
     * @param string $message
     */
    public static function error_log($message) {
        $prevtz = date_default_timezone_get();
        $tz = ini_get('date.timezone');
        if ($tz) {
            date_default_timezone_set($tz);
        }
        error_log($message);
        date_default_timezone_set($prevtz);
    }

    /**
     * Hook to capture BeforeSuite event so as to give access to moodle codebase.
     * This will try and catch any exception and exists if anything fails.
     *
     * @param BeforeSuiteScope $scope scope passed by event fired before suite.
     * @BeforeSuite
     */
    public static function before_suite_hook(BeforeSuiteScope $scope) {
        // If behat has been initialised then no need to do this again.
        if (self::$initprocessesfinished) {
            self::error_log('Behat suite start: ' . $scope->getSuite()->getName());
            return;
        }

        try {
            self::before_suite($scope);
        } catch (behat_stop_exception $e) {
            echo $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Gives access to moodle codebase, ensures all is ready and sets up the test lock.
     *
     * Includes config.php to use moodle codebase with $CFG->behat_*
     * instead of $CFG->prefix and $CFG->dataroot, called once per suite.
     *
     * @param BeforeSuiteScope $scope scope passed by event fired before suite.
     * @static
     * @throws behat_stop_exception
     */
    public static function before_suite(BeforeSuiteScope $scope) {
        global $CFG;

        // Defined only when the behat CLI command is running, the moodle init setup process will
        // read this value and switch to $CFG->behat_dataroot and $CFG->behat_prefix instead of
        // the normal site.
        if (!defined('BEHAT_TEST')) {
            define('BEHAT_TEST', 1);
        }

        if (!defined('CLI_SCRIPT')) {
            define('CLI_SCRIPT', 1);
        }

        // With BEHAT_TEST we will be using $CFG->behat_* instead of $CFG->dataroot, $CFG->prefix and $CFG->wwwroot.
        require_once(__DIR__ . '/../../../config.php');

        // Now that we are MOODLE_INTERNAL.
        require_once(__DIR__ . '/../../behat/classes/behat_command.php');
        require_once(__DIR__ . '/../../behat/classes/behat_selectors.php');
        require_once(__DIR__ . '/../../behat/classes/behat_context_helper.php');
        require_once(__DIR__ . '/../../behat/classes/util.php');
        require_once(__DIR__ . '/../../testing/classes/test_lock.php');
        require_once(__DIR__ . '/../../testing/classes/nasty_strings.php');

        // Avoids vendor/bin/behat to be executed directly without test environment enabled
        // to prevent undesired db & dataroot modifications, this is also checked
        // before each scenario (accidental user deletes) in the BeforeScenario hook.

        if (!behat_util::is_test_mode_enabled()) {
            throw new behat_stop_exception('Behat only can run if test mode is enabled. More info in ' .
                behat_command::DOCS_URL . '#Running_tests');
        }

        // Reset all data, before checking for check_server_status.
        // If not done, then it can return apache error, while running tests.
        behat_util::clean_tables_updated_by_scenario_list();
        behat_util::reset_all_data();

        // Check if server is running and using same version for cli and apache.
        behat_util::check_server_status();

        // Prevents using outdated data, upgrade script would start and tests would fail.
        if (!behat_util::is_test_data_updated()) {
            $commandpath = 'php admin/tool/behat/cli/init.php';
            throw new behat_stop_exception("Your behat test site is outdated, please run\n\n    " .
                    $commandpath . "\n\nfrom your moodle dirroot to drop and install the behat test site again.");
        }
        // Avoid parallel tests execution, it continues when the previous lock is released.
        test_lock::acquire('behat');

        // Store the browser reset time if reset after N seconds is specified in config.php.
        if (!empty($CFG->behat_restart_browser_after)) {
            // Store the initial browser session opening.
            self::$lastbrowsersessionstart = time();
        }

        if (!empty($CFG->behat_faildump_path) && !is_writable($CFG->behat_faildump_path)) {
            throw new behat_stop_exception('You set $CFG->behat_faildump_path to a non-writable directory');
        }

        // Handle interrupts on PHP7.
        if (extension_loaded('pcntl')) {
            $disabled = explode(',', ini_get('disable_functions'));
            if (!in_array('pcntl_signal', $disabled)) {
                declare(ticks = 1);
            }
        }

        // Handle interrupts on PHP7.
        if (extension_loaded('pcntl')) {
            $disabled = explode(',', ini_get('disable_functions'));
            if (!in_array('pcntl_signal', $disabled)) {
                declare(ticks = 1);
            }
        }

        // Totara: create or purge the error log file.
        $errorlog = ini_get('error_log');
        if (strpos($errorlog, 'behatrun') !== false) {
            $fp = fopen($errorlog, 'w');
            fclose($fp);
            self::error_log(''); // Add empty line to make the log more readable.
            self::error_log('Behat suite start: ' . $scope->getSuite()->getName());
        }
    }



    /**
     * Gives access to moodle codebase, to keep track of feature start time.
     *
     * @param BeforeFeatureScope $scope scope passed by event fired before feature.
     * @BeforeFeature
     */
    public static function before_feature(BeforeFeatureScope $scope) {
        // Totara: log scenario start for each new file.
        global $CFG;
        self::error_log('Feature start: ' . $scope->getFeature()->getTitle() . ' # ' . $scope->getFeature()->getFile());

        if (!defined('BEHAT_FEATURE_TIMING_FILE')) {
            return;
        }
        $file = $scope->getFeature()->getFile();
        self::$timings[$file] = microtime(true);
    }

    /**
     * Gives access to moodle codebase, to keep track of feature end time.
     *
     * @param AfterFeatureScope $scope scope passed by event fired after feature.
     * @AfterFeature
     */
    public static function after_feature(AfterFeatureScope $scope) {
        if (!defined('BEHAT_FEATURE_TIMING_FILE')) {
            return;
        }
        $file = $scope->getFeature()->getFile();
        self::$timings[$file] = microtime(true) - self::$timings[$file];
        // Probably didn't actually run this, don't output it.
        if (self::$timings[$file] < 1) {
            unset(self::$timings[$file]);
        }
    }

    /**
     * Gives access to moodle codebase, to keep track of suite timings.
     *
     * @param AfterSuiteScope $scope scope passed by event fired after suite.
     * @AfterSuite
     */
    public static function after_suite(AfterSuiteScope $scope) {
        // Totara: notify the suite was finished.
        self::error_log('Behat suite finish: ' . $scope->getSuite()->getName());

        if (!defined('BEHAT_FEATURE_TIMING_FILE')) {
            return;
        }
        $realroot = realpath(__DIR__.'/../../../').'/';
        foreach (self::$timings as $k => $v) {
            $new = str_replace($realroot, '', $k);
            self::$timings[$new] = round($v, 1);
            unset(self::$timings[$k]);
        }
        if ($existing = @json_decode(file_get_contents(BEHAT_FEATURE_TIMING_FILE), true)) {
            self::$timings = array_merge($existing, self::$timings);
        }
        arsort(self::$timings);
        @file_put_contents(BEHAT_FEATURE_TIMING_FILE, json_encode(self::$timings, JSON_PRETTY_PRINT));
    }

    /**
     * Resets the test environment.
     *
     * @BeforeScenario
     * @param BeforeScenarioScope $scope scope passed by event fired before scenario.
     * @throws behat_stop_exception If here we are not using the test database it should be because of a coding error
     */
    public function before_scenario(BeforeScenarioScope $scope) {
        global $DB, $CFG;

        // As many checks as we can.
        if (!defined('BEHAT_TEST') ||
            !defined('BEHAT_SITE_RUNNING') ||
            php_sapi_name() != 'cli' ||
            !behat_util::is_test_mode_enabled() ||
            !behat_util::is_test_site()) {
            throw new coding_exception('Behat only can modify the test database and the test dataroot!');
        }

        // Totara: Reset the browser if specified in config.php before we do anything.
        if (!empty($CFG->behat_restart_browser_after)) {
            if (self::$lastbrowsersessionstart + $CFG->behat_restart_browser_after < time()) {
                self::$forcerestart = true;
            }
        }

        // Totara: use any means to get things back up and running after any problems, browser restart should do the trick.
        if (self::$forcerestart) {
            for ($count = 0; $count < 3; $count++) {
                try {
                    $this->getMink()->stopSessions();
                    break;
                } catch (Exception $e) {
                    sleep(6);
                }
            }
            self::$forcerestart = false;
            self::$lastbrowsersessionstart = time();
        }

        $moreinfo = 'More info in ' . behat_command::DOCS_URL . '#Running_tests';
        $driverexceptionmsg = 'Selenium server is not running, you need to start it to run tests that involve Javascript. ' . $moreinfo;
        try {
            try {
                $session = $this->getSession();
            } catch (Exception $e) {
                // Totara: Let's try restarting the driver again, maybe this time it will work.
                try {
                    $this->getMink()->stopSessions();
                } catch (Exception $e) {
                }
                sleep(6);
                $session = $this->getSession();
            }
        } catch (CurlExec $e) {
            // Exception thrown by WebDriver, so only @javascript tests will be caugth; in
            // behat_util::check_server_status() we already checked that the server is running.
            throw new Exception($driverexceptionmsg, 0, $e);
        } catch (DriverException $e) {
            throw new Exception($driverexceptionmsg, 0, $e);
        } catch (UnknownError $e) {
            // Generic 'I have no idea' Selenium error. Custom exception to provide more feedback about possible solutions.
            $this->throw_unknown_exception($e, 0, $e);
        } catch (Exception $e) {
            throw new Exception(get_string('unknownexceptioninfo', 'tool_behat'), 0, $e);
        }

        $suitename = $scope->getSuite()->getName();

        // Register behat selectors for theme, if suite is changed. We do it for every suite change.
        if ($suitename !== self::$runningsuite) {
            behat_context_helper::set_environment($scope->getEnvironment());

            // We need the Mink session to do it and we do it only before the first scenario.
            $namedpartialclass = 'behat_partial_named_selector';
            $namedexactclass = 'behat_exact_named_selector';

            // If override selector exist, then set it as default behat selectors class.
            $overrideclass = behat_config_util::get_behat_theme_selector_override_classname($suitename, 'named_partial', true);
            if (class_exists($overrideclass)) {
                $namedpartialclass = $overrideclass;
            }

            // If override selector exist, then set it as default behat selectors class.
            $overrideclass = behat_config_util::get_behat_theme_selector_override_classname($suitename, 'named_exact', true);
            if (class_exists($overrideclass)) {
                $namedexactclass = $overrideclass;
            }

            $this->getSession()->getSelectorsHandler()->registerSelector('named_partial', new $namedpartialclass());
            $this->getSession()->getSelectorsHandler()->registerSelector('named_exact', new $namedexactclass());
        }

        // Reset mink session between the scenarios.
        try {
            $session->reset();
        } catch (Exception $e) {
            // Totara: This point is reached when Chrome driver freezes, let's give it one more kick.
            try {
                sleep(5);
                $session->restart();
                sleep(5);
                $session->reset();
            } catch (Exception $e) {
                throw new Exception('Resetting of Mink session failed', 0, $e);
            }
        }

        // Reset $SESSION.
        \core\session\manager::init_empty_session();

        // Ignore E_NOTICE and E_WARNING during reset, as this might be caused because of some existing process
        // running ajax. This will be investigated in another issue.
        $errorlevel = error_reporting();
        error_reporting($errorlevel & ~E_NOTICE & ~E_WARNING);
        behat_util::reset_all_data();
        error_reporting($errorlevel);

        // Assign valid data to admin user (some generator-related code needs a valid user).
        $user = $DB->get_record('user', array('username' => 'admin'));
        \core\session\manager::set_user($user);

        // Reset the browser if specified in config.php.
        if (!empty($CFG->behat_restart_browser_after) && $this->running_javascript()) {
            $now = time();
            if (self::$lastbrowsersessionstart + $CFG->behat_restart_browser_after < $now) {
                $session->restart();
                self::$lastbrowsersessionstart = $now;
            }
        }

        // Set the theme if not default.
        if ($suitename !== "default") {
            set_config('theme', $suitename);
            self::$runningsuite = $suitename;
        }

        // Start always in the the homepage.
        try {
            // Let's be conservative as we never know when new upstream issues will affect us.
            $session->visit($this->locate_path('/'));
        } catch (Exception $e) {
            // Totara: Something weird is going on, wait a bit and retry before stopping the whole run.
            try {
                $session->restart();
                sleep(5);
                $session->visit($this->locate_path('/'));
            } catch (Exception $e) {
                throw new Exception('Visiting of the main page failed', 0, $e);
            }
        }

        raise_memory_limit(MEMORY_EXTRA); // Totara includes very many files.

        // Checking that the root path is a Moodle test site.
        if (self::is_first_scenario()) {
            $notestsiteexception = new behat_stop_exception('The base URL (' . $CFG->wwwroot . ') is not a behat test site, ' .
                'ensure you started the built-in web server in the correct directory or your web server is correctly started and set up');
            $this->find("xpath", "//head/child::title[normalize-space(.)='" . behat_util::BEHATSITENAME . "']", $notestsiteexception);

            self::$initprocessesfinished = true;
        }

        // Run all test with medium (1024x768) screen size, to avoid responsive problems.
        try {
            $this->resize_window('medium');
        } catch (Exception $e) {
            throw new Exception('Error resizing the main page', 0, $e);
        }

        try {
            $this->wait_for_pending_js();
        } catch (Exception $e) {
            try {
                // We get here when devs do not close changed form at end of test and unsaved form data alert pops up.
                $session->restart();
                sleep(5);
                $session->visit($this->locate_path('/'));
                sleep(2);
                $this->wait_for_pending_js();
            } catch (Exception $e) {
                throw new Exception('Main JS page not initialied properly', 0, $e);
            }
        }
    }

    /**
     * Totara: nothing to do
     *
     * @BeforeStep
     */
    public function before_step_javascript(Behat\Behat\Hook\Scope\BeforeStepScope $scope) {
        // Totara: we do not know if the previous step failed or not,
        //         doing JS stuff makes no sense, do it after the step when we know the result!
    }

    /**
     * Wait for JS to complete after finishing the step.
     *
     * With this we ensure that there are not AJAX calls
     * still in progress.
     *
     * Executed only when running against a real browser. We wrap it
     * all in a try & catch to forward the exception to i_look_for_exceptions
     * so the exception will be at scenario level, which causes a failure, by
     * default would be at framework level, which will stop the execution of
     * the run.
     *
     * @param AfterStepScope $scope scope passed by event fired after step..
     * @AfterStep
     */
    public function after_step_javascript(AfterStepScope $scope) {
        global $CFG, $DB;

        // If step is undefined then throw exception, to get failed exit code.
        if ($scope->getTestResult()->getResultCode() === Behat\Behat\Tester\Result\StepResult::UNDEFINED) {
            throw new coding_exception("Step '" . $scope->getStep()->getText() . "'' is undefined.");
        }

        // Save the page content if the step failed.
        if (!empty($CFG->behat_faildump_path) &&
            $scope->getTestResult()->getResultCode() === Behat\Testwork\Tester\Result\TestResult::FAILED) {
            $this->take_contentdump($scope);
        }

        // Abort any open transactions to prevent subsequent tests hanging.
        // This does the same as abort_all_db_transactions(), but doesn't call error_log() as we don't
        // want to see a message in the behat output.
        if (($scope->getTestResult() instanceof \Behat\Behat\Tester\Result\ExecutedStepResult) &&
            $scope->getTestResult()->hasException()) {
            if ($DB && $DB->is_transaction_started()) {
                $DB->force_transaction_rollback();
            }
        }

        // Only run if JS.
        if (!$this->running_javascript()) {
            return;
        }

        // Save the page content if the step failed.
        if (!empty($CFG->behat_faildump_path) &&
            $scope->getTestResult()->getResultCode() === Behat\Testwork\Tester\Result\TestResult::FAILED) {
            $this->take_contentdump($scope);
        }

        // Abort any open transactions to prevent subsequent tests hanging.
        // This does the same as abort_all_db_transactions(), but doesn't call error_log() as we don't
        // want to see a message in the behat output.
        if (($scope->getTestResult() instanceof \Behat\Behat\Tester\Result\ExecutedStepResult) &&
            $scope->getTestResult()->hasException()) {
            if ($DB && $DB->is_transaction_started()) {
                $DB->force_transaction_rollback();
            }
        }

        // Only run if JS.
        if (!$this->running_javascript()) {
            return;
        }

        // Save a screenshot if the step failed.
        if (!empty($CFG->behat_faildump_path) &&
            $scope->getTestResult()->getResultCode() === \Behat\Testwork\Tester\Result\TestResult::FAILED) {
            $this->take_screenshot($scope);
        }

        // Totara: do not try to wait for anything here if not success, we are going to
        //         restart the browser anyway to make sure there are no leftovers.
        if ($scope->getTestResult()->getResultCode() === \Behat\Testwork\Tester\Result\TestResult::PASSED) {
            try {
                $this->wait_for_pending_js();
            } catch (Exception $e) {
                // If there is any problem the next step will fail
                // and we will restart the browser afterwards.
                // Do NOT close any alerts here, we want to know when stuff goes wrong!
                behat_hooks::$forcerestart = true;
            }
        }
    }

    /**
     * Executed after scenario having switch window to restart session.
     * This is needed to close all extra browser windows and starting
     * one browser window.
     *
     * @param AfterStepScope $scope Scope fired after step.
     * @AfterStep
     */
    public function after_step(AfterStepScope $scope) {
        // Totara: better restart browser after any failure to prevent cascading problems.
        if ($scope->getTestResult()->getResultCode() === \Behat\Testwork\Tester\Result\TestResult::FAILED) {
            self::$forcerestart = true;
            // Log failed steps.
            self::error_log('Failed step: ' . $scope->getStep()->getText() . ' # ' . $scope->getFeature()->getFile() . ':' .$scope->getStep()->getLine());
            $result = $scope->getTestResult();
            if ($result instanceof \Behat\Testwork\Tester\Result\ExceptionResult) {
                if ($result->hasException()) {
                    $ex = $result->getException();
                    self::error_log('Exception: ' . $ex->getMessage() . ' (' . get_class($ex) . ')');
                }
            }
        }
    }

    /**
     * Totara: nothing to do
     * Executed after scenario having switch window to restart session.
     * This is needed to close all extra browser windows and starting
     * one browser window.
     *
     * @param AfterScenarioScope $scope scope passed by event fired after scenario.
     * @AfterScenario @_switch_window
     */
    public function after_scenario_switchwindow(AfterScenarioScope $scope) {
        // Totara: let's use our own session restart tricks in the switch_to_window() step itself.
    }

    /**
     * Getter for self::$faildumpdirname
     *
     * @return string
     */
    protected function get_run_faildump_dir() {
        return self::$faildumpdirname;
    }

    /**
     * Take screenshot when a step fails.
     *
     * @throws Exception
     * @param AfterStepScope $scope scope passed by event after step.
     */
    protected function take_screenshot(AfterStepScope $scope) {
        // Goutte can't save screenshots.
        if (!$this->running_javascript()) {
            return false;
        }

        list ($dir, $filename) = $this->get_faildump_filename($scope, 'png');
        try {
            $this->saveScreenshot($filename, $dir);
        } catch (Exception $e) {
            // Totara: this must not throw exception!!!
            // Catching all exceptions as we don't know what the driver might throw.
            list ($dir, $filename) = $this->get_faildump_filename($scope, 'txt');
            $message = "Could not save screenshot due to an error\n" . $e->getMessage();
            file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $message);
        }

        // Totara: fix new file permissions!
        global $CFG;
        @chmod($dir . DIRECTORY_SEPARATOR . $filename, $CFG->filepermissions);
    }

    /**
     * Take a dump of the page content when a step fails.
     *
     * @throws Exception
     * @param AfterStepScope $scope scope passed by event after step.
     */
    protected function take_contentdump(AfterStepScope $scope) {
        list ($dir, $filename) = $this->get_faildump_filename($scope, 'html');

        $fh = fopen($dir . DIRECTORY_SEPARATOR . $filename, 'w');
        try {
            fwrite($fh, $this->getSession()->getPage()->getContent());
        } catch (Exception $e) {
            // Totara: this must not throw exception!!!
            fwrite($fh, $e->getMessage() . "\n" . $e->getTraceAsString());
        }
        fclose($fh);
        // Totara: fix new file permissions!
        global $CFG;
        @chmod($dir . DIRECTORY_SEPARATOR . $filename, $CFG->filepermissions);
    }

    /**
     * Determine the full pathname to store a failure-related dump.
     *
     * This is used for content such as the DOM, and screenshots.
     *
     * @param AfterStepScope $scope scope passed by event after step.
     * @param String $filetype The file suffix to use. Limited to 4 chars.
     */
    protected function get_faildump_filename(AfterStepScope $scope, $filetype) {
        global $CFG;

        // All the contentdumps should be in the same parent dir.
        if (!$faildumpdir = self::get_run_faildump_dir()) {
            $faildumpdir = self::$faildumpdirname = date('Ymd_His');

            $dir = $CFG->behat_faildump_path . DIRECTORY_SEPARATOR . $faildumpdir;

            if (!is_dir($dir) && !mkdir($dir, $CFG->directorypermissions, true)) {
                // It shouldn't, we already checked that the directory is writable.
                throw new Exception('No directories can be created inside $CFG->behat_faildump_path, check the directory permissions.');
            }
        } else {
            // We will always need to know the full path.
            $dir = $CFG->behat_faildump_path . DIRECTORY_SEPARATOR . $faildumpdir;
        }

        // The scenario title + the failed step text.
        // We want a i-am-the-scenario-title_i-am-the-failed-step.$filetype format.
        $filename = $scope->getFeature()->getTitle() . '_' . $scope->getStep()->getText();

        // As file name is limited to 255 characters. Leaving 5 chars for line number and 4 chars for the file.
        // extension as we allow .png for images and .html for DOM contents.
        $filenamelen = 245;

        // Suffix suite name to faildump file, if it's not default suite.
        $suitename = $scope->getSuite()->getName();
        if ($suitename != 'default') {
            $suitename = '_' . $suitename;
            $filenamelen = $filenamelen - strlen($suitename);
        } else {
            // No need to append suite name for default.
            $suitename = '';
        }

        $filename = preg_replace('/([^a-zA-Z0-9\_]+)/', '-', $filename);
        $filename = substr($filename, 0, $filenamelen) . $suitename . '_' . $scope->getStep()->getLine() . '.' . $filetype;

        return array($dir, $filename);
    }

    /**
     * Internal step definition to find exceptions, debugging() messages and PHP debug messages.
     *
     * Part of behat_hooks class as is part of the testing framework, is auto-executed
     * after each step so no features will splicitly use it.
     *
     * @Given /^I look for exceptions$/
     * @throw Exception Unknown type, depending on what we caught in the hook or basic \Exception.
     * @see Moodle\BehatExtension\EventDispatcher\Tester\ChainedStepTester
     */
    public function i_look_for_exceptions() {
        $this->look_for_exceptions();
    }

    /**
     * Returns whether the first scenario of the suite is running
     *
     * @return bool
     */
    protected static function is_first_scenario() {
        return !(self::$initprocessesfinished);
    }
}

/**
 * Behat stop exception
 *
 * This exception is thrown from before suite or scenario if any setup problem found.
 *
 * @package    core_test
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_stop_exception extends \Exception {
}
