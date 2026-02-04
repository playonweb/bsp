<?php

/**
 * User model
 */
class User extends BaseModel
{
	// Define necessary constants so we know from which table to load data
	const tableName = 'users';
	// ClassName constant is important for find and findOne static functions to work
	const className = 'User';

	private $validationErrors = [];

	// Create getter functions
	public function getName()
	{
		return $this->getField('name');
	}

	public function getEmail()
	{
		return $this->getField('email');
	}

	public function getCity()
	{
		return $this->getField('city');
	}

	public function getPhone()
	{
		return $this->getField('phone');
	}

	/**
	 * Get all users ordered by creation date
	 */
	public static function getAllUsers($db, $limit = null, $offset = 0)
	{
		$limitArr = $limit ? [$offset, $limit] : null;
		return self::find($db, '*', [], ['created_at' => 'DESC'], $limitArr);
	}

	/**
	 * Count all users
	 */
	public static function countAll($db)
	{
		$result = $db->query("SELECT COUNT(*) as count FROM users");
		return $result[0]['count'] ?? 0;
	}

	/**
	 * Search users by city
	 */
	public static function searchByCity($db, $city, $limit = null, $offset = 0)
	{
		$escaped = $db->mysqli->real_escape_string(trim($city));
		$query = "SELECT * FROM users WHERE city LIKE '%" . $escaped . "%' ORDER BY created_at DESC";

		if ($limit) {
			$query .= " LIMIT $offset, $limit";
		}

		return self::sql($db, $query);
	}

	/**
	 * Get users with data (efficient, fixes N+1)
	 */
	public static function getUsersData($db, $limit = 20, $offset = 0, $reveal = false)
	{
		$limitArr = $limit ? "LIMIT $offset, $limit" : "";
		$query = "SELECT * FROM users ORDER BY created_at DESC $limitArr";
		$rows = $db->query($query);
		return self::formatRows($rows, $reveal);
	}

	/**
	 * Search users with data (efficient, fixes N+1)
	 */
	public static function searchByCityData($db, $city, $limit = 20, $offset = 0, $reveal = false)
	{
		$escaped = $db->mysqli->real_escape_string(trim($city));
		$query = "SELECT * FROM users WHERE city LIKE '%$escaped%' ORDER BY created_at DESC";
		if ($limit) {
			$query .= " LIMIT $offset, $limit";
		}
		$rows = $db->query($query);
		return self::formatRows($rows, $reveal);
	}

	/**
	 * Format multiple rows efficiently
	 */
	private static function formatRows($rows, $reveal = false)
	{
		if (!is_array($rows))
			return [];
		return array_map(function ($row) use ($reveal) {
			$email = $row['email'] ?? '';
			$phone = $row['phone'] ?? null;
			if (!$reveal) {
				$email = self::obfuscateEmail($email);
				$phone = self::obfuscatePhone($phone);
			} else {
				$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
				$phone = $phone ? htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') : null;
			}
			return [
				'id' => (string) $row['id'],
				'name' => htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8'),
				'email' => $email,
				'city' => htmlspecialchars($row['city'] ?? '', ENT_QUOTES, 'UTF-8'),
				'phone' => $phone,
				'created_at' => $row['created_at'] ?? null
			];
		}, $rows);
	}

	/**
	 * Count users by city
	 */
	public static function countByCity($db, $city)
	{
		$escaped = $db->mysqli->real_escape_string(trim($city));
		$result = $db->query("SELECT COUNT(*) as count FROM users WHERE city LIKE '%$escaped%'");
		return $result[0]['count'] ?? 0;
	}

	/**
	 * Get user by ID
	 */
	public static function getUserById($db, $id)
	{
		if (!is_numeric($id)) {
			return null;
		}

		$users = self::find($db, '*', ['id' => (int) $id]);
		return !empty($users) ? $users[0] : null;
	}

	/**
	 * Create a new user with validation
	 */
	public static function createUser($db, $data)
	{
		$user = new self($db);
		$sanitized = self::sanitize($data);

		if (!$user->validate($sanitized)) {
			return [
				'success' => false,
				'errors' => $user->getValidationErrors()
			];
		}

		try {
			$user->insert($sanitized);
			return [
				'success' => true,
				'user' => $user
			];
		} catch (Exception $e) {
			error_log($e->getMessage());
			return [
				'success' => false,
				'errors' => ['general' => 'Failed to create user']
			];
		}
	}

	/**
	 * Validate user data
	 * @param array $data User data to validate
	 * @return bool True if valid, false otherwise
	 */
	public function validate($data)
	{
		$this->validationErrors = [];

		// Validate name
		if (empty($data['name']) || !is_string($data['name'])) {
			$this->validationErrors['name'] = 'Name is required and must be a string';
		} elseif (strlen($data['name']) < 2 || strlen($data['name']) > 255) {
			$this->validationErrors['name'] = 'Name must be between 2 and 255 characters';
		} elseif (!preg_match('/^[a-zA-Z\s\-\'\.]+$/u', $data['name'])) {
			$this->validationErrors['name'] = 'Name contains invalid characters';
		}

		// Validate email
		if (empty($data['email']) || !is_string($data['email'])) {
			$this->validationErrors['email'] = 'Email is required';
		} elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$this->validationErrors['email'] = 'Invalid email format';
		} elseif (strlen($data['email']) > 255) {
			$this->validationErrors['email'] = 'Email must not exceed 255 characters';
		}

		// Validate city
		if (empty($data['city']) || !is_string($data['city'])) {
			$this->validationErrors['city'] = 'City is required';
		} elseif (strlen($data['city']) < 2 || strlen($data['city']) > 255) {
			$this->validationErrors['city'] = 'City must be between 2 and 255 characters';
		} elseif (!preg_match('/^[a-zA-Z\s\-]+$/u', $data['city'])) {
			$this->validationErrors['city'] = 'City contains invalid characters';
		}

		// Validate phone (optional)
		if (!empty($data['phone'])) {
			// Normalize phone: remove spaces, dashes, parentheses
			$normalized = preg_replace('/[\s\-\(\)]/', '', $data['phone']);

			// Must start with + and contain only digits after that
			if (!preg_match('/^\+\d{7,15}$/', $normalized)) {
				$this->validationErrors['phone'] = 'Phone must be in format: +[country code][number] (7-15 digits total)';
			}
		}

		return empty($this->validationErrors);
	}

	/**
	 * Get validation errors
	 */
	public function getValidationErrors()
	{
		return $this->validationErrors;
	}

	/**
	 * Sanitize user input to prevent XSS
	 */
	public static function sanitize($data)
	{
		return [
			'name' => isset($data['name']) ? htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8') : '',
			'email' => isset($data['email']) ? filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL) : '',
			'city' => isset($data['city']) ? htmlspecialchars(trim($data['city']), ENT_QUOTES, 'UTF-8') : '',
			'phone' => isset($data['phone']) ? preg_replace('/[\s\-\(\)]/', '', trim($data['phone'])) : null
		];
	}

	/**
	 * Obfuscate email: j***@...
	 */
	public static function obfuscateEmail($email)
	{
		$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
		$parts = explode('@', $email);
		if (count($parts) === 2) {
			$name = $parts[0];
			$domain = $parts[1];
			$visible = substr($name, 0, 1);
			return $visible . '***@' . $domain;
		}
		return $email;
	}

	/**
	 * Obfuscate phone: +1-***...
	 */
	public static function obfuscatePhone($phone)
	{
		if (!$phone)
			return null;
		$phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
		return substr($phone, 0, 3) . '-***...';
	}

	/**
	 * Convert user to JSON:API format
	 */
	public function toJsonApi($reveal = false)
	{
		$email = $this->getEmail();
		$phone = $this->getPhone();

		if (!$reveal) {
			$email = self::obfuscateEmail($email);
			$phone = self::obfuscatePhone($phone);
		} else {
			$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
			$phone = $phone ? htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') : null;
		}

		return [
			'type' => 'users',
			'id' => (string) $this->getId(),
			'attributes' => [
				'name' => htmlspecialchars($this->getName(), ENT_QUOTES, 'UTF-8'),
				'email' => $email,
				'city' => htmlspecialchars($this->getCity(), ENT_QUOTES, 'UTF-8'),
				'phone' => $phone,
				'created_at' => $this->getField('created_at')
			],
			'links' => [
				'self' => '/api/v1/users/' . $this->getId()
			]
		];
	}

	/**
	 * Convert array of users to JSON:API format
	 * To fix N+1, use this to handle a collection efficiently
	 */
	public static function collectionToJsonApi($users, $reveal = false)
	{
		return array_map(function ($user) use ($reveal) {
			return $user->toJsonApi($reveal);
		}, $users);
	}
}