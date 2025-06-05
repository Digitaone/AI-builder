import requests
import time
import random
import argparse
import logging

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

def generate_traffic(urls: list[str], num_requests: int, delay: float, user_agents: list[str]):
    """
    Generates web traffic to a list of URLs.

    Args:
        urls: A list of URL strings.
        num_requests: An integer specifying how many requests to send to each URL.
        delay: A float representing the delay in seconds between requests.
        user_agents: A list of user-agent strings.
    """
    if not user_agents:
        logging.warning("User agents list is empty. Using a default user agent.")
        user_agents = ["Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"]

    for url in urls:
        logging.info(f"Starting traffic generation for URL: {url}")
        for i in range(num_requests):
            user_agent = random.choice(user_agents)
            headers = {"User-Agent": user_agent}
            try:
                logging.info(f"Sending request {i+1}/{num_requests} to {url} with User-Agent: {user_agent}")
                response = requests.get(url, headers=headers, timeout=10)
                # Using print for this specific output as it's a direct result, or could be logging.debug if too verbose
                print(f"Request {i+1}/{num_requests} to {url} | Status: {response.status_code} | User-Agent: {user_agent}")
                response.raise_for_status() # Raises an HTTPError for bad responses (4XX or 5XX)
            except requests.exceptions.ConnectionError as e:
                logging.error(f"Request {i+1}/{num_requests} to {url} failed (Connection Error): {e} | User-Agent: {user_agent}")
            except requests.exceptions.Timeout as e:
                logging.error(f"Request {i+1}/{num_requests} to {url} timed out: {e} | User-Agent: {user_agent}")
            except requests.exceptions.HTTPError as e:
                logging.error(f"Request {i+1}/{num_requests} to {url} failed (HTTP Error): {e} | User-Agent: {user_agent}")
            except requests.exceptions.RequestException as e:
                logging.error(f"Request {i+1}/{num_requests} to {url} failed (General Error): {e} | User-Agent: {user_agent}")

            time.sleep(delay)
        logging.info(f"Finished traffic generation for URL: {url}")

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description="Web Traffic Generator CLI")
    parser.add_argument(
        "--urls",
        required=True,
        nargs='+',
        help="List of URLs to send traffic to."
    )
    parser.add_argument(
        "--num-requests",
        type=int,
        default=10,
        help="Number of requests to send to each URL (default: 10)."
    )
    parser.add_argument(
        "--delay",
        type=float,
        default=1.0,
        help="Delay in seconds between requests (default: 1.0)."
    )
    parser.add_argument(
        "--user-agents",
        nargs='+',
        default=["Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"],
        help="List of user agent strings to use for requests."
    )

    args = parser.parse_args()

    generate_traffic(args.urls, args.num_requests, args.delay, args.user_agents)
