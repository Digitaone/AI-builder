# Web Traffic Generator

## Description
This script is a command-line tool designed to generate HTTP GET web traffic to specified URLs. It allows users to simulate multiple requests with configurable delays and user agents, which can be useful for testing web server performance, load balancing, and other web-related functionalities.

## Features
- Send HTTP GET requests to one or more URLs.
- Configure the number of requests to send to each URL.
- Set a delay (in seconds) between individual requests.
- Customize User-Agent strings for requests, with the ability to provide a list of user agents to be randomly selected.
- Basic logging of requests and errors.
- Command-line interface for easy execution and configuration.

## Requirements
- Python 3.6 or higher.
- Libraries:
    - `requests` (as listed in `requirements.txt`)

## Setup/Installation
1.  **Clone the repository (Optional):**
    If you have git, you can clone the repository:
    ```bash
    git clone <repository_url>
    cd web_traffic_generator
    ```
    Otherwise, ensure you have the project files in a local directory.

2.  **Install dependencies:**
    Navigate to the root directory of the project (`web_traffic_generator`) and install the required Python libraries:
    ```bash
    pip install -r requirements.txt
    ```

## Usage
The script is run from the command line from within the `web_traffic_generator` directory.

### Command-Line Arguments
-   `--urls URLS [URLS ...]`: (Required) A list of one or more URLs to send traffic to.
    Example: `--urls http://example.com https://google.com`
-   `--num-requests NUM_REQUESTS`: (Optional) The number of requests to send to each specified URL.
    Default: `10`
    Example: `--num-requests 50`
-   `--delay DELAY`: (Optional) The delay in seconds between consecutive requests.
    Default: `1.0`
    Example: `--delay 0.5`
-   `--user-agents USER_AGENTS [USER_AGENTS ...]`: (Optional) A list of user agent strings to be randomly used for the requests. If not provided, a default user agent is used.
    Example: `--user-agents "MyCustomAgent/1.0" "AnotherAgent/2.0"`

### Example Command
Here's an example of how to run the script to send 50 requests to `http://example.com` and `https://google.com` with a delay of 0.5 seconds between requests, using the default user agent:
```bash
python src/traffic_generator.py --urls http://example.com https://google.com --num-requests 50 --delay 0.5
```

To use custom user agents:
```bash
python src/traffic_generator.py --urls http://example.com --num-requests 20 --user-agents "Mozilla/5.0 (compatible; MyTestBot/0.1)" "WebAppTester/1.1"
```

### Getting Help
To see the full list of commands and their descriptions:
```bash
python src/traffic_generator.py --help
```

## Running Tests
Unit tests are located in the `tests` directory and can be run using Python's `unittest` module. From the root `web_traffic_generator` directory, execute:
```bash
python -m unittest discover -s tests
```
Alternatively, you can run a specific test file:
```bash
python -m unittest tests.test_traffic_generator
```

## Contributing
Contributions are welcome! Feel free to fork the repository, make your changes, and submit a pull request. If you find any issues or have suggestions for improvements, please open an issue on the project's issue tracker.

## License
This project is open source and available under the MIT License.
(You can add a `LICENSE` file with the MIT License text if desired).
