#!/usr/bin/env python
# coding: utf-8

# pip install selenium
# #https://chromedriver.chromium.org/downloads

# In[ ]:


from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from time import sleep
import pandas as pd
import datetime, os
import random


# In[ ]:


from selenium.webdriver.chrome.options import Options
opts = Options()
#ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36"
from fake_useragent import UserAgent
ua = UserAgent()
opts.add_argument("user-agent={}".format(ua.random)) 
opts.add_argument("--no-sandbox")
opts.add_argument("--headless")


# In[ ]:


def check_KW(str1):
    KW_LL = ['資費','電信','通訊','5G','訊號','方案','網速','上網','流量','斷訊','下載','限速','遠傳','中華電','台灣大','亞太電','台星']
    print(str1)
    for KW in KW_LL:
        if str1.find(KW) >= 0:
            return True
    return False


# In[ ]:



#keyword = '遠傳電信 site:mobile01.com after:2021-12-01 before:2021-12-01'
def google_search(keyword):
    #產生新瀏覽器，自動的。
    #s = Service('chromedriver')
    s = Service('/var/www/html/Fruit/application/third_party/chromedriver')
    
    driver = webdriver.Chrome(service=s,options =opts)
    #driver.set_window_position(0, 0)
    #driver.set_window_size(600, 600)

    driver.get("https://www.google.com.tw/")
    driver.implicitly_wait(5)
    
    # 找到「搜尋」的元素，輸入要搜尋的關鍵字並且送出
    search_input = driver.find_element(By.NAME,"q")
    #search_input.send_keys(u'遠傳電信 site:mobile01.com after:2021-12-01 before:2021-12-01')
    search_input.send_keys(keyword)
    search_input.submit()
    
    tmp_L1 = []
    
    #只找前10頁
    page_count = 10

    try:
        for page_i in range(page_count):
            page_i+=1
            print('Google第'+str(page_i)+'頁')
            eles = driver.find_elements(By.CSS_SELECTOR, 'a')
            print(len(eles))
            for ele in eles:
                #print(ele.text)
                if type(ele.get_attribute('href')) == type('A'):
                    if ele.text.find('obile01') >= 0 and check_KW(ele.text) > 0:
                        print(ele.text)
                        print(ele.get_attribute('href'))
                        
                        tmp_L1.append(ele.get_attribute('href'))
                        #tmp_L1 += Mobile01_article(ele.get_attribute('href'))
                        print('-'*10)

            #換頁
            driver.find_element(By.CSS_SELECTOR,"a[id='pnnext']").click()
    except Exception as e:
        #print(e.text)
        pass

    # 關閉瀏覽器
    finally:
        driver.quit()
        
    print('總計:'+str(len(tmp_L1))+'篇主文。')

    #print('Google Done:('+str(len(tmp_L1))+'筆):'+keyword)
    return tmp_L1


# In[ ]:


def Mobile01_article(url):    
    tmp_LL = []
    
    s2 = Service('chromedriver')
    #s2 = Service('/var/www/html/Fruit/application/third_party/chromedriver')
    driver2 = webdriver.Chrome(service=s2, options =opts)
    driver2.get(url)
    
    page_flag = 0
    
    try:
        title_name = driver2.find_element(By.CLASS_NAME,'t2').text
        print(title_name)
        items = driver2.find_elements(By.XPATH, "//div[@class='l-articlePage']")
        print('items:'+str(len(items)))
    except Exception as e:
        items = []

    
    for i in range(len(items)):
        try:
            if page_flag == 0:
                #主文
                #下兩行數字311&312，看Cell範例架構
                times = driver2.find_element(By.XPATH,"//div[@class='l-publish__content']/div[3]/div[1]/span[1]").text
                floors = driver2.find_element(By.XPATH,"//div[@class='l-publish__content']/div[3]/div[1]/span[2]").text
                reads = driver2.find_element(By.XPATH,"//div[@class='c-iconLink']/span").text

                page_flag = 1

            else:
                #回文
                times = items[i].find_element(By.XPATH,"./descendant::div[@class='l-navigation__item']/span[1]").text
                floors = items[i].find_element(By.XPATH,"./descendant::div[@class='l-navigation__item']/span[2]").text
                reads = 0

            ids = items[i].find_element(By.XPATH,"./descendant::div[@class='c-authorInfo__id']/a").text
            contents = items[i].find_element(By.XPATH,"./descendant::article").text
            contents.replace(",", "，")
            likes = items[i].find_element(By.XPATH,"./descendant::div[@class='c-tool']/label/span").text
            scores = items[i].find_element(By.XPATH,"./descendant::div[@class='c-authorInfo__score']").text
            
            link = url

            #發文者，標題，推回文類別，內容，發文時間，觀看次數，推噓評價，發文者分數，URL

            if floors == '#1' :
                con_type = '主文'
            else:
                con_type = '回文'
            tmp_L1 = [ids,title_name, con_type, contents, times,  reads, likes, scores, link]
            #print(tmp_L1)
            tmp_LL.append(tmp_L1)
        except Exception as e:
            continue
        

    driver2.close()
    
    return tmp_LL


# In[ ]:





# In[ ]:


#今天
TD = datetime.date.today()
#目標日:昨天
Target_date =  TD - datetime.timedelta(days=1) 
TD =  Target_date + datetime.timedelta(days=1) 


KW_LL = ['遠傳電信 site:www.mobile01.com after:'+str(Target_date)+' before:'+str(TD),
         '中華電信 site:www.mobile01.com after:'+str(Target_date)+' before:'+str(TD),
         '台灣大哥大 site:www.mobile01.com after:'+str(Target_date)+' before:'+str(TD),
         '亞太電信 site:www.mobile01.com after:'+str(Target_date)+' before:'+str(TD),
         '台灣之星 site:www.mobile01.com after:'+str(Target_date)+' before:'+str(TD)]

url_LL = []

for KW in KW_LL:
    print('確認一下:'+KW)
    tmp_LL = google_search(KW)
    print('Total:'+str(len(tmp_LL))+' rows.')
    url_LL = url_LL + tmp_LL

url_SS = set(url_LL)
    
acticle_LL = []
print('文章數:'+str(len(url_SS)))
for url in url_SS:
    try:
        tmp_LL =  Mobile01_article(url)
        print('tmp_LL:'+str(len(tmp_LL)))
        acticle_LL = acticle_LL + tmp_LL
    except Exception as e:
        print('Error1.')
        continue

# Modile回覆內容，List轉成 DataFrame
df_per_topic = pd.DataFrame(acticle_LL, columns =['發文者','標題','推回文類別','內容','發文時間','觀看次數','推噓評價','發文者分數','URL']) 
df_per_topic['內容'] = df_per_topic['內容'].str.replace('\n',' ')
df_per_topic['內容'] = df_per_topic['內容'].str.replace('\r',' ')


# In[ ]:


#df_per_topic.head()


# In[ ]:


df_per_topic.to_csv("/var/www/html/Fruit/pubCache/Mobile01_"+str(TD)+".csv", index = False)
#df_per_topic.to_csv("Mobile01_"+str(TD)+".csv")
print('Done.')


# In[ ]:





# In[ ]:





# In[ ]:





# In[ ]:




