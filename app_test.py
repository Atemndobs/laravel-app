import re
from playwright.sync_api import Page, expect


def test_homepage_has_Playwright_in_title_and_get_started_link_linking_to_the_intro_page(page: Page):
    page.goto("https://accounts.spotify.com/authorize?client_id=2a6ebf75ebb84b258107d56ea4694183&redirect_uri=http%3A%2F%2Fmage.tech%3A8899%2Fapi%2Fspotify%2Fcallback&response_type=code&scope=playlist-read-private+user-read-private&state=bf88b5df5e327e10")


# print out the url
print(Page.url)


#     # Expect a title "to contain" a substring.
#     expect(page).to_have_title(re.compile("Playwright"))
#
#     # create a locator
#     get_started = page.get_by_role("link", name="Get started")
#
#     # Expect an attribute "to be strictly equal" to the value.
#     expect(get_started).to_have_attribute("href", "/docs/intro")
#
#     # Click the get started link.
#     get_started.click()
#
#     # Expects the URL to contain intro.
#     expect(page).to_have_url(re.compile(".*intro"))