#!/usr/bin/env python
# coding: utf-8

# In[ ]:


import requests, json

from fake_useragent import UserAgent
ua = UserAgent()

import pandas as pd
import datetime
import time
import random
from bs4 import BeautifulSoup


# 發文者，標題，推回文類別，內容，發文時間，觀看次數，推噓評價，發文者分數，URL

# In[ ]:


#今天
TD = datetime.date.today()
#目標日:昨天
YD =  TD - datetime.timedelta(days=1) 

KeyWord_LL = ['遠傳','中華電信','台灣大哥大','亞太','台灣之星']
Total_LL = []
for KW in KeyWord_LL :
    time.sleep(random.randint(1,5)) 
    print(KW+' Start.')
    re = requests.get('https://udn.com/search/word/2/'+KW , headers= {'user-agent':ua.random})
    soup = BeautifulSoup(re.text, 'html.parser')
    
    text_tags = soup.find_all('div',  {"class": "story-list__text"} )
    for tag in text_tags:
        try:
            date_str = tag.find('time').string[:10]
            print('date1:'+date_str)
            print('date2:'+str(YD))
            if date_str == str(YD) :                
                text_url = tag.find('a', href=True)['href']
                if text_url.find('story') > 0 :
                    print('Get News:'+text_url )
                    re1 = requests.get(text_url, headers= {'user-agent':ua.random})
                    soup1 = BeautifulSoup(re1.text, 'html.parser')

                    title_tag = soup1.find('h1', {"class":"article-content__title"})
                    #print(title_tag.string)

                    article = soup1.select('html script[type="application/ld+json"]')[0].contents[0]
                    results = json.loads(article)
                    author = results[0]['author']['name']
                    publish_time = results[0]['datePublished'][:19].replace('T',' ')
                    print('{},{}'.format(author,publish_time))

                    cont = ""
                    for row in soup1.find('section', {"class":"article-content__editor"}).find_all('p'):
                        cont+= row.text.replace('\r',' ').replace('\t',' ').replace('\n',' ')
                    #print(cont[:10])
                #發文者，標題，推回文類別，內容，發文時間，觀看次數，推噓評價，發文者分數，URL
                Total_LL.append([author,title_tag.string,'主文',cont,publish_time,'','','',text_url])
                print('----')           

        except:
            print('tag字串不符時間格式.')
            #continue

UDN_news_df = pd.DataFrame(Total_LL, columns =['Writter','Title','主/推文','內容','發文時間','觀看次數', '推噓評價','發文者分數','URL']) 


# In[ ]:


UDN_news_df.to_csv("../cache/UDNnews_"+str(TD)+".csv")


# In[ ]:





# In[ ]:





# In[ ]:





# In[ ]:


#UDN_news_df


# In[ ]:




