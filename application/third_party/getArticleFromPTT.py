# -*- coding: UTF-8 -*-
import requests, re, csv, math, os
import time, datetime
from bs4 import BeautifulSoup
class pttCrawler(object):
    pttURL = 'https://www.ptt.cc'
    pttKeyWrodUrl = 'https://www.ptt.cc/bbs/MobileComm/search?page='
    keyWordList = []
    allSearchPage = [] # 存所有 search keyword href
    allTitleHref = {} # 存所有文章 href
    allTitle = []
    allPush = []
    allContent = []
    startTimestamp = ""
    endTimestamp = ""
    csvWriter = object

    def __init__(self):
        self.keyWordList = []
        self.allSearchPage = []
        f = open('/var/www/html/Fruit/pubCache/ptt_' + str(datetime.date.today()) + '.csv', 'w', encoding='utf8')
        self.csvWriter = csv.writer(f)
        self.csvWriter.writerow(["發文者", "標題", "推回文類別", "內容", "發文時間", "觀看次數", "推噓評價", "發文者分數", "URL"])

    # 取得所有關鍵字的 page
    def parseGO(self):
        self.__getTimeRange()
        for keyWord in self.keyWordList:
            print("當前搜尋關鍵字 : " + keyWord)
            self.__parseAllPageUrl(keyWord)
        if (len(self.allTitleHref) > 0): self.__parseContent()

    def __getTimeRange(self):
        today = datetime.date.today()
        timeRange = datetime.timedelta(days = 1)
        yesterdayStart = str(today-timeRange) + " 00:00:00"
        yesterdayEnd = str(today-timeRange) + " 23:59:59"
        self.startTimestamp = int(time.mktime(time.strptime(yesterdayStart, "%Y-%m-%d %H:%M:%S")))
        self.endTimestamp = int(time.mktime(time.strptime(yesterdayEnd, "%Y-%m-%d %H:%M:%S")))

    def __parseAllPageUrl(self, keyWord):
        resp = requests.get(url = self.pttKeyWrodUrl + '1&q=' + keyWord, cookies={'over18': '1'}, verify=True, timeout=15)
        if resp.status_code == 200:
            soup = BeautifulSoup(resp.text, 'html.parser')
            pageLinks = soup.find_all("a", "btn wide")
            maxPageCounter = math.ceil((int(re.search('\d+', pageLinks[0]["href"]).group(0)) + 1) / 10)

            self.allSearchPage = []
            for i in range(1, maxPageCounter): self.allSearchPage.append(self.pttKeyWrodUrl + str(i) + '&q=' + keyWord)
            print("當前關鍵字需檢視頁面 : " + str(len(self.allSearchPage)))
            if (len(self.allSearchPage) > 0): self.__parseAllTitleUrl()
        else:
            print('invalid url:', resp.url)

    def __parseAllTitleUrl(self):
        for pageLink in self.allSearchPage:
            resp = requests.get(pageLink, cookies={'over18': '1'}, verify=True, timeout=3)
            if resp.status_code != 200: continue
            soup = BeautifulSoup(resp.text, 'html.parser')
            divs = soup.find_all("div", "title")
            for div in divs:
                articleTimestamp = int(re.search('\/M\.(\d+)', div.find("a")["href"]).group(1))
                if (articleTimestamp < self.startTimestamp or articleTimestamp > self.endTimestamp): break
                self.allTitleHref[div.find("a")["href"]] = div.find("a").text
        print("目前文章數量 : " + str(len(self.allTitleHref)))

    def __parseContent(self):
        for pageLink in self.allTitleHref:
            title = self.allTitleHref[pageLink]
            self.allTitle.append(title)
            longPageLink = self.pttURL + pageLink
            resp = requests.get(longPageLink, cookies={'over18': '1'}, verify=True, timeout=15)
            if resp.status_code != 200: continue
            print(longPageLink + " " + title)
            soup = BeautifulSoup(resp.text, 'html.parser')

            spanArticleMetaValue = soup.find_all("span", "article-meta-value")
            if (len(spanArticleMetaValue) == 0):
                spanArticleMetaValue = soup.find_all("span", "b4")
                if (len(spanArticleMetaValue) == 4):
                    userId = re.sub(" \(.+\)", "", spanArticleMetaValue[0].text.strip())
                    pullDateTime = re.sub(" \(.+\)", "", spanArticleMetaValue[3].text.strip())
            elif (len(spanArticleMetaValue) == 3):
                userId = re.sub(" \(.+\)", "", spanArticleMetaValue[0].text)
                pullDateTime = re.sub(" \(.*\)", "", spanArticleMetaValue[2].text)
            elif (len(spanArticleMetaValue) == 4):
                userId = re.sub(" \(.+\)", "", spanArticleMetaValue[0].text)
                pullDateTime = re.sub(" \(.*\)", "", spanArticleMetaValue[3].text)



            tempContent = ''.join(re.split(", ", re.split(r'※ 發信站:', soup.find(id='main-content').text.replace("\n", ", "))[0])[1:]).replace(",", "，")
            self.allContent.append(tempContent)
            self.csvWriter.writerow([userId, title, "主文", tempContent, pullDateTime, "N/A", "N/A", "N/A", longPageLink])

            spansPushUserId = soup.find_all("span", "f3 hl push-userid")
            spansContents = soup.find_all("span", "f3 push-content")
            spanPushDateTime = soup.find_all("span", "push-ipdatetime")


            tempAllPush = {}
            tempAllPushDateTime = {}
            for index, span in enumerate(spansPushUserId):
                userId = span.text.strip()
                userPushContent = spansContents[index].text.strip().replace(": ", "")
                userPushContent = spansContents[index].text.strip().replace(": ", "")
                if userId not in tempAllPush: tempAllPush[userId] = []
                tempAllPush[userId].append(userPushContent)
                tempAllPushDateTime[userId] = spanPushDateTime[index].text.replace("\n", "")

            for userId in tempAllPush:
                tempPush = ''.join(tempAllPush[userId]).replace(",", "，")
                self.allPush.append(tempPush)
                self.csvWriter.writerow([userId, title, "推文", tempPush, tempAllPushDateTime[userId], "N/A", "N/A", "N/A", longPageLink])


if __name__ == '__main__':
    c = pttCrawler()
    c.pttKeyWrodUrl = 'https://www.ptt.cc/bbs/MobileComm/search?page='
    c.keyWordList = ["遠傳", "中華", "台哥", "台灣大", "亞太", "台灣之星", "台星"]
    c.parseGO()
    #print(c.allSearchPage)
    #print(c.allTitleHref)
    #print(c.allTitle)
    #print("########################################################")
    #print(c.allContent)
