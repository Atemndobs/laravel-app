import sys
import argparse
import webbrowser

def open_url(url):

    print('Number of arguments:', len(sys.argv), 'arguments.')
    print('Argument List:', str(sys.argv))
    webbrowser.open(url)


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('url', help='URL to open in the web browser')
    args = parser.parse_args()

    open_url(args.url)
