import os
path = "input"
i = 0
for filename in os.listdir(path):
	os.rename(os.path.join(path, filename), os.path.join(path, str(i)+"ash.jpg"))
	i+=1

j = 0
for filename in os.listdir(path):
	os.rename(os.path.join(path, filename), os.path.join(path, str(j)+"fgfg.jpg"))
	j+=1