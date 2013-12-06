<?php /* Developed by Rafael Perrella for unit testing purposes. */

class Assert
{
	private static $assert_tests = array();

	private static function backtrace($index)
	{
		$index++;
		$backtrace = debug_backtrace();
		if (isset($backtrace[$index]))
		{
			$backtrace[$index]["line"] = $backtrace[$index > 0 ? $index-1 : $index]["line"];
			$backtrace[$index]["file"] = $backtrace[$index > 0 ? $index-1 : $index]["file"];
			return $backtrace[$index];
		}
		$argv = isset($GLOBALS["argv"]) ? $GLOBALS["argv"] : array();
		return array("line" => $backtrace[sizeof($backtrace)-1]["line"], "file" => get_included_files()[0], "function" => "main", "args" => $argv);
	}

	private static function extract_assert_location($info)
	{
		$path = str_replace("\\", "/", $info["file"]);
		$line = $info["line"];

		$function = $info["function"];
		if (isset($info["class"]) && $info["class"] !== null)
			$function = $info["class"] . "." . $function;

		return array("line" => $line, "path" => $path, "function" => $function);
	}

	private static function add_test_($location, $error_message=null)
	{
		if (!isset(self::$assert_tests[$location["path"]][$location["function"]]) || self::$assert_tests[$location["path"]][$location["function"]][1] == null)
			self::$assert_tests[$location["path"]][$location["function"]] = array($location, $error_message);
		return true;
	}

	private static function add_test($info, $error_message=null)
	{
		return self::add_test_(self::extract_assert_location($info), $error_message);
	}

	private static function add_time($line, $path, $function, $class=null, $time=0)
	{
		$location = self::extract_assert_location(array("line" => $line, "file" => $path, "function" => $function, "class" => $class));
		self::add_test_($location);
		self::$assert_tests[$location["path"]][$location["function"]][2] = $time;
	}

	private static function get_value_string($value)
	{
		return "(" . var_export($value, true) . ")";
	}

	public static function assertEquals($value, $expectedValue, $backtrace_index=1)
	{
		if ($value == $expectedValue)
			return self::add_test(self::backtrace($backtrace_index));
		self::add_test(self::backtrace($backtrace_index), "Expected value " . self::get_value_string($expectedValue) . " but got " . self::get_value_string($value) . ".");
		return false;
	}

	public static function assertNotEquals($value, $expectedValue, $backtrace_index=1)
	{
		if ($value != $expectedValue)
			return self::add_test(self::backtrace($backtrace_index));
		self::add_test(self::backtrace($backtrace_index), "Expected anything not equal to " . self::get_value_string($expectedValue) . " but got " . self::get_value_string($value) . ".");
		return false;
	}

	public static function assertIdentical($value, $expectedValue, $backtrace_index=1)
	{
		if ($value === $expectedValue)
			return self::add_test(self::backtrace($backtrace_index));
		self::add_test(self::backtrace($backtrace_index), "Expected exact value " . self::get_value_string($expectedValue) . " but got " . self::get_value_string($value) . ".");
		return false;
	}

	public static function assertNotIdentical($value, $expectedValue, $backtrace_index=1)
	{
		if ($value !== $expectedValue)
			return self::add_test(self::backtrace($backtrace_index));
		self::add_test(self::backtrace($backtrace_index), "Expected anything not identical to " . self::get_value_string($expectedValue) . " but got " . self::get_value_string($value) . ".");
		return false;
	}

	public static function assertFalse($value, $backtrace_index=1)
	{
		if ($value === false)
			return self::add_test(self::backtrace($backtrace_index));
		self::add_test(self::backtrace($backtrace_index), "Expected value (false) but got " . self::get_value_string($value) . ".");
		return false;
	}

	public static function assertTrue($value, $backtrace_index=1)
	{
		if ($value === true)
			return self::add_test(self::backtrace($backtrace_index));
		self::add_test(self::backtrace($backtrace_index), "Expected value (true) but got " . self::get_value_string($value) . ".");
		return false;
	}

	public static function assertNull($value, $backtrace_index=1)
	{
		if ($value === NULL)
			return self::add_test(self::backtrace($backtrace_index));
		self::add_test(self::backtrace($backtrace_index), "Expected value (NULL) but got " . self::get_value_string($value) . ".");
		return false;
	}

	public static function assertNotNull($value, $backtrace_index=1)
	{
		if ($value !== NULL)
			return self::add_test(self::backtrace($backtrace_index));
		self::add_test(self::backtrace($backtrace_index), "Expected anything not identical to (NULL) but got (NULL).");
		return false;
	}

	public static function fail($message, $backtrace_index=1)
	{
		self::add_test(self::backtrace($backtrace_index), $message);
		return false;
	}

	public static function get_assert_tests()
	{
		return self::$assert_tests;
	}

	public static function clear()
	{
		self::$assert_tests = array();
	}

	public static function dump()
	{
		var_dump(self::$assert_tests);
	}

	public static function display_errors($clear=true, $display_time=false)
	{
		echo "<style>"; require_once("punit.css"); echo "</style>";
		//echo '<link rel="stylesheet" type="text/css" href="punit.css" />';

		$cnt = 0;
		foreach (self::$assert_tests as $path => $functions)
		{
			echo '<div class="punit">';

			$success = true;
			foreach ($functions as $function => $data)
				if ($data[1] !== null)
					$success = false;

			echo '<div class="path' . ($success ? "_success" : "_error") . '">' . $path . '</div>';
			echo '<div class="tab' . ($success ? "_success" : "_error") . '"></div>';
			echo '<div class="line' . ($success ? "_success" : "_error") . '"><b>Line</b></div>';
			echo '<div class="function' . ($success ? "_success" : "_error") . '"><b>Function</b></div>';
			echo '<div class="message' . ($success ? "_success" : "_error") . '"><b>Error message</b></div>';
			echo '<div class="right"></div>';

			foreach ($functions as $function => $data)
			{
				$time = ($display_time && isset(self::$assert_tests[$path][$function][2]) ? round(self::$assert_tests[$path][$function][2], 3) : "0") . " s";
				$success = self::$assert_tests[$path][$function][1] === null;
				$data = self::$assert_tests[$path][$function];

				$line = $data[0]["line"]; // $data[0] is the location (path, function, line)
				$error_message = $data[1];

				echo '<div class="tab' . ($success ? "_success" : "_error") . '"></div>';
				echo '<div class="line' . ($success ? "_success" : "_error") . '">' . $line . '</div>';
				echo '<div class="function' . ($success ? "_success" : "_error") . '">' . $function . ($display_time ? " (" . $time . ")" : "") . '</div>';
				echo '<div class="message' . ($success ? "_success" : "_error") . '">' . $error_message . '</div>';
				echo '<div class="right"></div>';
			}

			echo '</div>'; // [class="punit"]
			echo '<br /><div class="separator"></div>';
		}

		if ($clear)
			Assert::clear();
	}

	public static function test_class($class_name, $display_errors=true)
	{
		$setUp = "setUp"; // method for setUp
		$file_name = (new ReflectionClass($class_name))->getFileName();

		$obj = new $class_name();
		$class_methods = get_class_methods($class_name);

		foreach ($class_methods as $method_name)
		{
			if ($method_name == $setUp)
				continue;
			if (method_exists($obj, $setUp) && is_callable(array($obj, $setUp)))
				$obj->$setUp();
			if (is_callable(array($obj, $method_name)))
			{
				$time = microtime(true);
				$obj->$method_name();
				self::add_time((new ReflectionMethod($class_name, $method_name))->getStartLine(), $file_name, $method_name, $class_name, microtime(true) - $time);
			}
		}

		if ($display_errors)
			self::display_errors(true, true);
	}
};

function assertEquals($value, $expectedValue) {
	return Assert::assertEquals($value, $expectedValue, 2);
}
function assertNotEquals($value, $expectedValue) {
	return Assert::assertNotEquals($value, $expectedValue, 2);
}
function assertIdentical($value, $expectedValue) {
	return Assert::assertIdentical($value, $expectedValue, 2);
}
function assertNotIdentical($value, $expectedValue) {
	return Assert::assertNotIdentical($value, $expectedValue, 2);
}
function assertFalse($value) {
	return Assert::assertFalse($value, 2);
}
function assertTrue($value) {
	return Assert::assertTrue($value, 2);
}
function assertNull($value) {
	return Assert::assertNull($value, 2);
}
function assertNotNull($value) {
	return Assert::assertNotNull($value, 2);
}
function fail($message) {
	return Assert::fail($message, 2);
}

// HOW TO USE
/*
	1. Create a file.
	2. Include punit.php and the class to be tested.
	3. Create a class.
	4. Create (at least) one public method with some tests
	4.1. Use fail(*) assert*(*) methods to make your tests
	4.1.1. If you want to avoid the other tests in a method when a test fails, use if/return:
		assert*(*); // becomes:
		if (!assert*(*)) return;
	4.2. You can create a public method setUp(), that will be executed before EACH test method.
	5. Outside (and after) the class {}, run Assert::test_class("YourClassName");.

	P.S.: You don't necessarily need to test only classes, but to make tests easier to write and read,
		you still should put them in a class.
*/

// EXAMPLE
/*
	<?php
	require_once("punit.php");
	require_once("myClass.php");
	class myClassTest {
		public function myTest1() {
			$obj = new myClass();
			if (!assertTrue($obj->is_something())) return;
			if (!assertEquals($obj->get_something(), "something")) return;
		}
	};
	Assert::test_class("myClassTest");
	?>
*/

?>