import unittest
from unittest.mock import patch, MagicMock, call
import logging
from web_traffic_generator.src.traffic_generator import generate_traffic
# Temporarily disable logging for cleaner test output
# You might want to capture log output in more sophisticated tests
logging.disable(logging.CRITICAL)

class TestTrafficGenerator(unittest.TestCase):

    @patch('web_traffic_generator.src.traffic_generator.requests.get')
    @patch('web_traffic_generator.src.traffic_generator.time.sleep')
    @patch('web_traffic_generator.src.traffic_generator.logging')
    def test_basic_successful_execution(self, mock_logging, mock_sleep, mock_requests_get):
        mock_response = MagicMock()
        mock_response.status_code = 200
        mock_requests_get.return_value = mock_response

        urls = ["http://example.com", "http://example.org"]
        num_requests = 2
        delay = 0.1
        user_agents = ["UA1", "UA2"]

        generate_traffic(urls, num_requests, delay, user_agents)

        self.assertEqual(mock_requests_get.call_count, len(urls) * num_requests)

        # Check that all URLs were called
        called_urls = [args[0][0] for args in mock_requests_get.call_args_list]
        for url in urls:
            self.assertTrue(any(url in called_url for called_url in called_urls))

        # Check that user agents are from the list
        for single_call in mock_requests_get.call_args_list:
            headers = single_call[1].get('headers', {})
            self.assertIn(headers.get('User-Agent'), user_agents)

        self.assertEqual(mock_sleep.call_count, len(urls) * num_requests)
        mock_sleep.assert_called_with(delay)
        mock_response.raise_for_status.assert_called()


    @patch('web_traffic_generator.src.traffic_generator.requests.get')
    @patch('web_traffic_generator.src.traffic_generator.time.sleep')
    @patch('web_traffic_generator.src.traffic_generator.logging')
    def test_request_connection_error(self, mock_logging, mock_sleep, mock_requests_get):
        from requests.exceptions import ConnectionError
        mock_requests_get.side_effect = ConnectionError("Test connection error")

        urls = ["http://example.com"]
        num_requests = 1
        delay = 0.1
        user_agents = ["UA1"]

        generate_traffic(urls, num_requests, delay, user_agents)

        self.assertEqual(mock_requests_get.call_count, 1)
        mock_logging.error.assert_called_once()
        self.assertIn("failed (Connection Error)", mock_logging.error.call_args[0][0])
        mock_sleep.assert_called_once_with(delay)

    @patch('web_traffic_generator.src.traffic_generator.requests.get')
    @patch('web_traffic_generator.src.traffic_generator.time.sleep')
    @patch('web_traffic_generator.src.traffic_generator.logging')
    def test_request_timeout_error(self, mock_logging, mock_sleep, mock_requests_get):
        from requests.exceptions import Timeout
        mock_requests_get.side_effect = Timeout("Test timeout error")

        urls = ["http://example.com"]
        num_requests = 1
        delay = 0.1
        user_agents = ["UA1"]

        generate_traffic(urls, num_requests, delay, user_agents)
        self.assertEqual(mock_requests_get.call_count, 1)
        mock_logging.error.assert_called_once()
        self.assertIn("timed out", mock_logging.error.call_args[0][0])
        mock_sleep.assert_called_once_with(delay)

    @patch('web_traffic_generator.src.traffic_generator.requests.get')
    @patch('web_traffic_generator.src.traffic_generator.time.sleep')
    @patch('web_traffic_generator.src.traffic_generator.logging')
    def test_request_http_error(self, mock_logging, mock_sleep, mock_requests_get):
        from requests.exceptions import HTTPError
        mock_response = MagicMock()
        mock_response.status_code = 404
        mock_response.raise_for_status.side_effect = HTTPError("Test HTTP error")
        mock_requests_get.return_value = mock_response

        urls = ["http://example.com/notfound"]
        num_requests = 1
        delay = 0.1
        user_agents = ["UA1"]

        generate_traffic(urls, num_requests, delay, user_agents)
        self.assertEqual(mock_requests_get.call_count, 1)
        mock_logging.error.assert_called_once()
        self.assertIn("failed (HTTP Error)", mock_logging.error.call_args[0][0])
        mock_sleep.assert_called_once_with(delay)


    @patch('web_traffic_generator.src.traffic_generator.requests.get')
    @patch('web_traffic_generator.src.traffic_generator.time.sleep')
    @patch('web_traffic_generator.src.traffic_generator.logging')
    def test_delay_between_requests(self, mock_logging, mock_sleep, mock_requests_get):
        mock_response = MagicMock()
        mock_response.status_code = 200
        mock_requests_get.return_value = mock_response

        urls = ["http://example.com"]
        num_requests = 3
        delay = 0.5
        user_agents = ["UA1"]

        generate_traffic(urls, num_requests, delay, user_agents)

        self.assertEqual(mock_sleep.call_count, num_requests)
        # Check that sleep was called with the specified delay each time
        for call_arg in mock_sleep.call_args_list:
            self.assertEqual(call_arg[0][0], delay)


    @patch('web_traffic_generator.src.traffic_generator.requests.get')
    @patch('web_traffic_generator.src.traffic_generator.time.sleep')
    @patch('web_traffic_generator.src.traffic_generator.logging')
    def test_empty_urls_list(self, mock_logging, mock_sleep, mock_requests_get):
        urls = []
        num_requests = 5
        delay = 0.1
        user_agents = ["UA1"]

        generate_traffic(urls, num_requests, delay, user_agents)

        mock_requests_get.assert_not_called()
        mock_sleep.assert_not_called()
        # Check if logging.info was called with "Starting traffic generation..." or "Finished..."
        # This depends on the implementation, currently it won't log anything if urls is empty before the loop.
        # If there was a log message like "No URLs provided", we could check for that.
        # For now, we just ensure no requests are made.
        # We can check that no error logs were made related to processing.
        mock_logging.error.assert_not_called()


    @patch('web_traffic_generator.src.traffic_generator.requests.get')
    @patch('web_traffic_generator.src.traffic_generator.time.sleep')
    @patch('web_traffic_generator.src.traffic_generator.logging')
    def test_empty_user_agents_list(self, mock_logging, mock_sleep, mock_requests_get):
        mock_response = MagicMock()
        mock_response.status_code = 200
        mock_requests_get.return_value = mock_response

        urls = ["http://example.com"]
        num_requests = 1
        delay = 0.1
        user_agents = [] # Empty list

        generate_traffic(urls, num_requests, delay, user_agents)

        self.assertEqual(mock_requests_get.call_count, 1)
        mock_logging.warning.assert_called_once_with("User agents list is empty. Using a default user agent.")

        # Verify that a default user agent was used
        headers = mock_requests_get.call_args[1].get('headers', {})
        self.assertIn('User-Agent', headers)
        self.assertTrue(len(headers['User-Agent']) > 0) # Check if a default UA string is present

if __name__ == '__main__':
    unittest.main()
