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
	public static function getAllUsers($db)
	{
		return self::find($db, '*', [], ['created_at' => 'DESC']);
	}

	/**
	 * Search users by city
	 */
	public static function searchByCity($db, $city)
	{
		$escaped = $db->mysqli->real_escape_string(trim($city));
		return self::sql($db, "SELECT * FROM users WHERE city LIKE '%$escaped%' ORDER BY created_at DESC");
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
	 * Get all unique cities
	 */
	public static function getCities($db)
	{
		$result = $db->query("SELECT DISTINCT city FROM users WHERE city IS NOT NULL AND city != '' ORDER BY city ASC");
		$cities = [];
		foreach ($result as $row) {
			$cities[] = $row['city'];
		}
		return $cities;
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
	 * Convert user to JSON:API format
	 */
	public function toJsonApi()
	{
		return [
			'type' => 'users',
			'id' => (string) $this->getId(),
			'attributes' => [
				'name' => htmlspecialchars($this->getName(), ENT_QUOTES, 'UTF-8'),
				'email' => htmlspecialchars($this->getEmail(), ENT_QUOTES, 'UTF-8'),
				'city' => htmlspecialchars($this->getCity(), ENT_QUOTES, 'UTF-8'),
				'phone' => $this->getPhone() ? htmlspecialchars($this->getPhone(), ENT_QUOTES, 'UTF-8') : null
			],
			'links' => [
				'self' => '/api/v1/users/' . $this->getId()
			]
		];
	}

	/**
	 * Convert array of users to JSON:API format
	 */
	public static function collectionToJsonApi($users)
	{
		return array_map(function ($user) {
			return $user->toJsonApi();
		}, $users);
	}
}