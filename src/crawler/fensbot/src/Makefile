BASEDIR:=.

include fensbot.make

MF:=

SUBDIR=utils interf fetch

all: config.h options.h subdirs-all fensbot

subdirs-all:
	for d in $(SUBDIR); do (cd $$d; $(MAKE) all); done

clean distclean dep prof debug: options.h config.h 
	for d in $(SUBDIR); do (cd $$d; $(MAKE) $@-in); done
	$(MAKE) $@-here

fensbot: $(ABS-UTILS-OBJ) $(ABS-FETCH-OBJ) $(ABS-INTERF-OBJ) $(ABS-MAIN-OBJ)
	$(CXX) $(MF) $(LIBS) -o fensbot $(ABS-UTILS-OBJ) \
	$(ABS-FETCH-OBJ) $(ABS-INTERF-OBJ) $(ABS-MAIN-OBJ) ../adns/libadns.a

dep-here:
	makedepend -f- -I$(BASEDIR) -Y *.cc 2> /dev/null > .depend

redo-here: fensbot

debug-here: CXXFLAGS += -g
debug-here: MF += -g
debug-here: redo-here

prof-here: CXXFLAGS += -pg -DPROF
prof-here: MF += -pg
prof-here: redo-here

clean-here:
	$(RM) *~ *.o
	$(RM) *.bak
	$(RM) fensbot

distclean-here: clean-here
	$(RM) options.h config.h .depend

options.h: ../options.h
	cp ../options.h .

config.h: ../config.h
	cp ../config.h .
