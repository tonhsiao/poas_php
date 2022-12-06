#!/usr/bin/env python
# coding: utf-8

# In[ ]:


import requests, re, json

from fake_useragent import UserAgent
ua = UserAgent()

import pandas as pd
import datetime
import time
import random


# In[ ]:


def jsontxt(str1):
    try:
        return json.loads(str1.replace('\r',' ' ).replace('\t',' ' ).replace('\n',' ' ))
    except ValueError as e:
        #print('json Error.')
        print(e)
        print(str1)
        return []


# In[ ]:


def check_KW(str1):
    KW_LL = ['資費','電信','通訊','5G','訊號','網速','上網','流量','斷訊','下載速度','限速','遠傳','中華電','台灣大哥大','亞太','台灣之星','台星']
    for KW in KW_LL:
        #print(KW)
        if str1.find(KW) >= 0:
            return True
    return False


# In[ ]:


TD = datetime.date.today()
Target_date =  TD - datetime.timedelta(days=1) 
re = requests.get('https://www.dcard.tw/service/api/v2/posts?popular=false&limit=100', headers= {'user-agent':ua.random})    


# In[ ]:


con_LL = jsontxt(re.text)
Target_LL = []
if len(con_LL) > 0 :
    for L1 in con_LL:
        #print(L1['title'])
        start_date = datetime.datetime.strptime(L1['createdAt'][:10],'%Y-%m-%d').date()
        if check_KW(L1['title']) and start_date ==  Target_date :
            print(L1['title'])  
            Target_LL.append([L1['title'],str(L1['id'])])
            
    start_date = datetime.datetime.strptime(L1['createdAt'][:10],'%Y-%m-%d').date()
    print(str(start_date) + ' vs ' + str(Target_date) )    
    while start_date >= Target_date:
        time.sleep(60)
        re1 = requests.get('https://www.dcard.tw/service/api/v2/posts?popular=false&limit=100&before='+tmp_id, headers= {'user-agent':ua.random})
        con_LL1 = jsontxt(re1.text)
        if len(con_LL1) > 0 :
            for L1 in con_LL1:
                #print(L1['title'])
                start_date = datetime.datetime.strptime(L1['createdAt'][:10],'%Y-%m-%d').date()
                if check_KW(L1['title']) and start_date ==  Target_date :
                    print(L1['title'])  
                    Target_LL.append([L1['title'],str(L1['id'])])    
    
        print('Last id ='+str(L1['id']))
        
        tmp_id = str(L1['id'])
        start_date = datetime.datetime.strptime(L1['createdAt'][:10],'%Y-%m-%d').date()
        print(str(start_date) + ' vs ' + str(Target_date) ) 
print('Part1 Done.')


# In[ ]:


def get_content(con_id):
    con_LL = []
    time.sleep(15)
    msg_url = 'https://www.dcard.tw/service/api/v2/posts/'+con_id
    re1 = requests.get(msg_url, headers= {'user-agent':ua.random})

    con_L1 = jsontxt(re1.text)

    #發文時間
    con_time = con_L1['createdAt'][:19].replace('T',' ') 
    
    #發文者
    if 'school' in con_L1.keys() :
        con_writer = con_L1['school']
    else:
        con_writer = 'null'

    if 'department' in con_L1.keys() :
        con_writer+=  "-"+con_L1['department']

    #標題
    con_title = con_L1['title']

    #發文內容
    con_msg = con_L1['content']        

    #推噓評價:
    con_like = con_L1['likeCount']
    
    con_LL.append([con_writer, con_title, '主文', con_msg, con_time, '', con_like, '' , msg_url])
    
    print('target_id:'+con_id+',開始找推文')

    #以下是推文
    time.sleep(15)
    reply_url = 'https://www.dcard.tw/service/api/v2/posts/'+con_id+'/comments'
    re2 = requests.get(reply_url, headers= {'user-agent':ua.random})
    
    con_L2 = jsontxt(re2.text)
    if len(con_L2) > 0 :
        for L2 in con_L2:
            #發文內容
            if 'school' in L2.keys() :
                con_msg = L2['content']   
            else:#沒內容直接略過
                break

            #發文者
            if 'school' in L2.keys() :
                con_writer = L2['school']
            else:
                con_writer = 'null'

            if 'department' in L2.keys() :
                con_writer+=  "-"+L2['department']

            #發文時間
            con_time = L2['createdAt'][:19].replace('T',' ') 

            #推噓評價:
            con_like = L1['likeCount']

            con_LL.append([con_writer, con_title, '回文', con_msg, con_time, '', con_like, '' , msg_url])
    
    print('target_id:'+con_id+',共 '+ str(len(con_LL)) + '篇')
    return con_LL


# In[ ]:


Total_LL = []
for row_LL in Target_LL:
    Total_LL+= get_content(row_LL[1])
print('Part2 Done.')
Dcard_df = pd.DataFrame(Total_LL, columns =['發文者','標題','推回文類別','內容','發文時間','觀看次數','推噓評價','發文者分數','URL']) 
    


# In[ ]:


Dcard_df.to_csv("/var/www/html/Fruit/pubCache/Dcard_"+str(TD)+".csv", index = False)
#Dcard_df.to_csv("UDNnews_"+str(TD)+".csv", index = False)


# In[ ]:





# In[ ]:





# In[ ]:





# In[ ]:





# In[ ]:





# In[ ]:




